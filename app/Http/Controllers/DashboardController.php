<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with statistics, low stock alerts and the category chart.
     */
    public function index(): View
    {
        $threshold = Medicine::LOW_STOCK_THRESHOLD;

        $totalMedicines = Medicine::count();
        $totalStock = (int) Medicine::sum('stock');
        $totalCategories = Category::count();
        $lowStockCount = Medicine::where('stock', '<=', $threshold)->count();

        $lowStockMedicines = Medicine::with('category')
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->orderBy('name')
            ->get();

        $categoryStats = Category::withCount('medicines')
            ->orderByDesc('medicines_count')
            ->orderBy('name')
            ->get();

        $maxPerCategory = max((int) $categoryStats->max('medicines_count'), 1);

        $recentMedicines = Medicine::with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'totalMedicines' => $totalMedicines,
            'totalStock' => $totalStock,
            'totalCategories' => $totalCategories,
            'lowStockCount' => $lowStockCount,
            'lowStockMedicines' => $lowStockMedicines,
            'categoryStats' => $categoryStats,
            'maxPerCategory' => $maxPerCategory,
            'recentMedicines' => $recentMedicines,
            'threshold' => $threshold,
        ]);
    }
}
