<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MedicineController extends Controller
{
    /**
     * List medicines with search and category filter.
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $search = trim((string) $request->query('q'));
        $categoryId = (int) $request->query('category');

        $query = Medicine::with('category');

        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('unit', 'like', $like)
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', $like));
            });
        }

        if ($categoryId > 0 && $categories->contains('id', $categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $medicines = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('medicines.index', [
            'medicines' => $medicines,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * Show the create medicine form.
     */
    public function create(): View
    {
        return view('medicines.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created medicine.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        DB::transaction(function () use ($validated) {
            $medicine = Medicine::create($validated);

            if ($medicine->stock > 0) {
                $medicine->movements()->create([
                    'type' => StockMovement::TYPE_IN,
                    'quantity' => $medicine->stock,
                    'description' => 'Stok awal',
                ]);
            }
        });

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Data obat berhasil ditambahkan.');
    }

    /**
     * Show a single medicine with its stock history.
     */
    public function show(Request $request, Medicine $medicine): View
    {
        $movements = $medicine->movements()
            ->with('medicine')
            ->paginate(10)
            ->withQueryString();

        return view('medicines.show', [
            'medicine' => $medicine->load('category'),
            'movements' => $movements,
        ]);
    }

    /**
     * Show the edit medicine form.
     */
    public function edit(Medicine $medicine): View
    {
        return view('medicines.edit', [
            'medicine' => $medicine->load('category'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the given medicine.
     */
    public function update(Request $request, Medicine $medicine): RedirectResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        DB::transaction(function () use ($medicine, $validated) {
            $oldStock = (int) $medicine->stock;
            $newStock = (int) $validated['stock'];

            $medicine->fill($validated);
            $medicine->save();

            if ($newStock !== $oldStock) {
                $medicine->movements()->create([
                    'type' => $newStock > $oldStock ? StockMovement::TYPE_IN : StockMovement::TYPE_OUT,
                    'quantity' => abs($newStock - $oldStock),
                    'description' => 'Penyesuaian stok saat edit data obat',
                ]);
            }
        });

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Data obat berhasil diperbarui.');
    }

    /**
     * Remove the given medicine.
     */
    public function destroy(Medicine $medicine): RedirectResponse
    {
        $medicine->delete();

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Data obat berhasil dihapus.');
    }

    /**
     * Validation rules shared by store and update.
     *
     * @return array<string, array<int, string>>
     */
    private function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'price' => ['required', 'integer', 'min:0'],
            'expiration_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Indonesian validation messages.
     *
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'name.required' => 'Nama obat wajib diisi.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka.',
            'stock.min' => 'Stok tidak boleh kurang dari 0.',
            'unit.required' => 'Satuan wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',
            'expiration_date.required' => 'Tanggal kadaluarsa wajib diisi.',
            'expiration_date.date' => 'Tanggal kadaluarsa tidak valid.',
        ];
    }
}
