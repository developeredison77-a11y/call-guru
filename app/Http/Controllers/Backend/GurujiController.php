<?php

namespace App\Http\Controllers\Backend;

use App\Enums\UserTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateManagedUserRequest;
use App\Services\Backend\ManagedUserService;
use App\Support\ManagedUsers\ManagedUserModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GurujiController extends Controller
{
    public function __construct(
        private readonly ManagedUserService $managedUsers,
    ) {}

    public function index(): View
    {
        return view('users.index', [
            'users' => $this->managedUsers->list(UserTypeEnum::Guruji),
            'module' => ManagedUserModule::gurujis(),
        ]);
    }

    public function edit(int $guruji): View
    {
        return view('users.edit', [
            'managedUser' => $this->managedUsers->find($guruji, UserTypeEnum::Guruji),
            'module' => ManagedUserModule::gurujis(),
        ]);
    }

    public function update(UpdateManagedUserRequest $request, int $guruji): RedirectResponse
    {
        $user = $this->managedUsers->find($guruji, UserTypeEnum::Guruji);
        $this->managedUsers->update($user, $request->editableAttributes());

        return redirect()->route('gurujis.index')->with('status', 'Guruji updated successfully.');
    }

    public function toggleStatus(int $guruji): RedirectResponse
    {
        $user = $this->managedUsers->find($guruji, UserTypeEnum::Guruji);
        $this->managedUsers->toggleStatus($user);

        return back()->with('status', 'Guruji status updated successfully.');
    }

    public function destroy(int $guruji): RedirectResponse
    {
        $user = $this->managedUsers->find($guruji, UserTypeEnum::Guruji);
        $this->managedUsers->softDelete($user);

        return back()->with('status', 'Guruji deleted successfully.');
    }
}
