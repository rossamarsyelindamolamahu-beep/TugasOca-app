<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Show the medicine report with search, category and stock status filters.
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $search = trim((string) $request->query('q'));
        $categoryId = (int) $request->query('category');
        $status = in_array($request->query('status'), ['low', 'safe'], true)
            ? $request->query('status')
            : null;

        $threshold = Medicine::LOW_STOCK_THRESHOLD;

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

        if ($status === 'low') {
            $query->where('stock', '<=', $threshold);
        } elseif ($status === 'safe') {
            $query->where('stock', '>', $threshold);
        }

        $medicines = $query->orderBy('name')->get();

        return view('reports.index', [
            'medicines' => $medicines,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
            'status' => $status,
            'threshold' => $threshold,
        ]);
    }
}
