<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Models\Language;
use App\Services\Backend\ContentManagementService;
use App\Support\DashboardContent\ContentModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LanguageController extends Controller
{
    public function __construct(
        private readonly ContentManagementService $content,
    ) {}

    public function index(): View
    {
        return view('content.index', [
            'records' => $this->content->list(Language::class),
            'module' => ContentModule::languages(),
        ]);
    }

    public function create(): View
    {
        return view('content.form', [
            'module' => ContentModule::languages(),
        ]);
    }

    public function store(LanguageRequest $request): RedirectResponse
    {
        $this->content->create(Language::class, [
            ...$request->validated(),
            'status' => 1,
        ]);

        return redirect()->route('languages.index')->with('status', 'Language created successfully.');
    }

    public function edit(Language $language): View
    {
        return view('content.form', [
            'record' => $language,
            'module' => ContentModule::languages(),
        ]);
    }

    public function update(LanguageRequest $request, Language $language): RedirectResponse
    {
        $this->content->update($language, $request->validated());

        return redirect()->route('languages.index')->with('status', 'Language updated successfully.');
    }

    public function toggleStatus(Language $language): RedirectResponse
    {
        $this->content->toggleStatus($language);

        return back()->with('status', 'Language status updated successfully.');
    }

    public function destroy(Language $language): RedirectResponse
    {
        $this->content->softDelete($language);

        return back()->with('status', 'Language deleted successfully.');
    }
}
