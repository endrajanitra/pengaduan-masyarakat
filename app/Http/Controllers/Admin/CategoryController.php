<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Daftar semua kategori — bisa diakses admin ke atas.
     */
    public function index(): View
    {
        $categories = Category::withCount('complaints')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Form tambah kategori baru.
     */
    public function create(): View
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        return view('admin.categories.form');
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name'],
            'icon'        => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        Category::create(array_merge($validated, [
            'slug'      => Str::slug($validated['name']),
            'is_active' => true,
        ]));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Form edit kategori.
     */
    public function edit(Category $category): View
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update kategori.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'icon'        => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['boolean'],
        ]);

        $category->update(array_merge($validated, [
            'slug' => Str::slug($validated['name']),
        ]));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Toggle aktif/nonaktif kategori.
     */
    public function toggleActive(Category $category): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $category->update(['is_active' => ! $category->is_active]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kategori \"{$category->name}\" berhasil {$status}.");
    }
}