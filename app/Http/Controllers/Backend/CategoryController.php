<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\Backend\ContentManagementService;
use App\Support\DashboardContent\ContentModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ContentManagementService $content,
    ) {}

    public function index(): View
    {
        return view('content.index', [
            'records' => $this->content->list(Category::class),
            'module' => ContentModule::categories(),
        ]);
    }

    public function create(): View
    {
        return view('content.form', [
            'module' => ContentModule::categories(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->content->create(Category::class, [
            ...$request->validated(),
            'status' => 1,
        ]);

        return redirect()->route('categories.index')->with('status', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('content.form', [
            'record' => $category,
            'module' => ContentModule::categories(),
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->content->update($category, $request->validated());

        return redirect()->route('categories.index')->with('status', 'Category updated successfully.');
    }

    public function toggleStatus(Category $category): RedirectResponse
    {
        $this->content->toggleStatus($category);

        return back()->with('status', 'Category status updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->content->softDelete($category);

        return back()->with('status', 'Category deleted successfully.');
    }
}
