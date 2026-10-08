<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * List categories with search.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $query = Category::withCount('medicines');

        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('categories.index', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    /**
     * Show the create category form.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori tersebut sudah ada.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ]);

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show the edit category form.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the given category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori tersebut sudah ada.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the given category when it is not used by any medicine.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $usedBy = $category->medicines()->count();

        if ($usedBy > 0) {
            return back()->with(
                'error',
                "Kategori \"{$category->name}\" tidak dapat dihapus karena masih digunakan oleh {$usedBy} obat."
            );
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
