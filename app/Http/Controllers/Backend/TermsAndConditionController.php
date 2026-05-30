<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\TermsAndConditionRequest;
use App\Models\TermsAndCondition;
use App\Services\Backend\ContentManagementService;
use App\Support\DashboardContent\ContentModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TermsAndConditionController extends Controller
{
    public function __construct(
        private readonly ContentManagementService $content,
    ) {}

    public function index(): View
    {
        return view('content.index', [
            'records' => $this->content->list(TermsAndCondition::class),
            'module' => ContentModule::termsAndConditions(),
        ]);
    }

    public function create(): View
    {
        return view('content.form', [
            'module' => ContentModule::termsAndConditions(),
        ]);
    }

    public function store(TermsAndConditionRequest $request): RedirectResponse
    {
        $this->content->create(TermsAndCondition::class, [
            ...$request->validated(),
            'status' => 1,
        ]);

        return redirect()->route('terms-and-conditions.index')->with('status', 'Terms and Conditions created successfully.');
    }

    public function edit(TermsAndCondition $termsAndCondition): View
    {
        return view('content.form', [
            'record' => $termsAndCondition,
            'module' => ContentModule::termsAndConditions(),
        ]);
    }

    public function update(TermsAndConditionRequest $request, TermsAndCondition $termsAndCondition): RedirectResponse
    {
        $this->content->update($termsAndCondition, $request->validated());

        return redirect()->route('terms-and-conditions.index')->with('status', 'Terms and Conditions updated successfully.');
    }

    public function toggleStatus(TermsAndCondition $termsAndCondition): RedirectResponse
    {
        $this->content->toggleStatus($termsAndCondition);

        return back()->with('status', 'Terms and Conditions status updated successfully.');
    }

    public function destroy(TermsAndCondition $termsAndCondition): RedirectResponse
    {
        $this->content->softDelete($termsAndCondition);

        return back()->with('status', 'Terms and Conditions deleted successfully.');
    }
}
