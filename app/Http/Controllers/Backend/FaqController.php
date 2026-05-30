<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use App\Services\Backend\ContentManagementService;
use App\Support\DashboardContent\ContentModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct(
        private readonly ContentManagementService $content,
    ) {}

    public function index(): View
    {
        return view('content.index', [
            'records' => $this->content->list(Faq::class),
            'module' => ContentModule::faqs(),
        ]);
    }

    public function create(): View
    {
        return view('content.form', [
            'module' => ContentModule::faqs(),
        ]);
    }

    public function store(FaqRequest $request): RedirectResponse
    {
        $this->content->create(Faq::class, [
            ...$request->validated(),
            'status' => 1,
        ]);

        return redirect()->route('faqs.index')->with('status', 'FAQ created successfully.');
    }

    public function edit(Faq $faq): View
    {
        return view('content.form', [
            'record' => $faq,
            'module' => ContentModule::faqs(),
        ]);
    }

    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $this->content->update($faq, $request->validated());

        return redirect()->route('faqs.index')->with('status', 'FAQ updated successfully.');
    }

    public function toggleStatus(Faq $faq): RedirectResponse
    {
        $this->content->toggleStatus($faq);

        return back()->with('status', 'FAQ status updated successfully.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $this->content->softDelete($faq);

        return back()->with('status', 'FAQ deleted successfully.');
    }
}
