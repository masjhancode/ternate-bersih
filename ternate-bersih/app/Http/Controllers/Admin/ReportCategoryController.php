<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCategory;
use Illuminate\Http\Request;

class ReportCategoryController extends Controller
{
    public function index()
    {
        $categories = ReportCategory::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sla_hours' => 'required|integer|min:1',
        ]);

        ReportCategory::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Kategori Sampah berhasil ditambahkan.');
    }

    public function edit(ReportCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, ReportCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sla_hours' => 'required|integer|min:1',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Kategori Sampah berhasil diperbarui.');
    }

    public function destroy(ReportCategory $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori Sampah berhasil dihapus.');
    }
}
