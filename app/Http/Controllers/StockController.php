<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Show the stock management page (form + movement history).
     */
    public function index(Request $request): View
    {
        $medicines = Medicine::with('category')->orderBy('name')->get();

        $type = in_array($request->query('type'), ['in', 'out'], true)
            ? $request->query('type')
            : null;

        $query = StockMovement::with('medicine')->latest();

        if ($type !== null) {
            $query->where('type', $type);
        }

        $movements = $query->paginate(10)->withQueryString();

        $lowStockMedicines = Medicine::where('stock', '<=', Medicine::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->get();

        return view('stocks.index', [
            'medicines' => $medicines,
            'movements' => $movements,
            'type' => $type,
            'lowStockMedicines' => $lowStockMedicines,
        ]);
    }

    /**
     * Record a stock movement (masuk/keluar) and update the medicine stock.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'medicine_id' => ['required', 'integer', 'exists:medicines,id'],
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'medicine_id.required' => 'Obat wajib dipilih.',
            'medicine_id.exists' => 'Obat yang dipilih tidak valid.',
            'type.required' => 'Jenis pergerakan stok wajib dipilih.',
            'type.in' => 'Jenis pergerakan stok tidak valid.',
            'quantity.required' => 'Jumlah stok wajib diisi.',
            'quantity.integer' => 'Jumlah stok harus berupa angka.',
            'quantity.min' => 'Jumlah stok minimal 1.',
        ]);

        $quantity = (int) $validated['quantity'];
        $isIncoming = $validated['type'] === StockMovement::TYPE_IN;

        $unit = DB::transaction(function () use ($validated, $quantity, $isIncoming) {
            /** @var Medicine $medicine */
            $medicine = Medicine::whereKey($validated['medicine_id'])->lockForUpdate()->firstOrFail();

            if (! $isIncoming && $quantity > $medicine->stock) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok keluar melebihi stok tersedia (sisa stok {$medicine->stock} {$medicine->unit}).",
                ]);
            }

            $medicine->stock = $isIncoming
                ? $medicine->stock + $quantity
                : $medicine->stock - $quantity;

            $medicine->save();

            $medicine->movements()->create([
                'type' => $validated['type'],
                'quantity' => $quantity,
                'description' => $validated['description'] ?? null,
            ]);

            return $medicine->unit;
        });

        return redirect()
            ->route('stocks.index')
            ->with('success', ($isIncoming ? 'Stok masuk' : 'Stok keluar')." {$quantity} {$unit} berhasil dicatat.");
    }
}
