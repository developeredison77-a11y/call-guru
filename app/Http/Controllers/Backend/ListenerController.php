<?php

namespace App\Http\Controllers\Backend;

use App\Enums\UserTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateManagedUserRequest;
use App\Services\Backend\ManagedUserService;
use App\Support\ManagedUsers\ManagedUserModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ListenerController extends Controller
{
    public function __construct(
        private readonly ManagedUserService $managedUsers,
    ) {}

    public function index(): View
    {
        return view('users.index', [
            'users' => $this->managedUsers->list(UserTypeEnum::Listener),
            'module' => ManagedUserModule::listeners(),
        ]);
    }

    public function edit(int $listener): View
    {
        return view('users.edit', [
            'managedUser' => $this->managedUsers->find($listener, UserTypeEnum::Listener),
            'module' => ManagedUserModule::listeners(),
        ]);
    }

    public function update(UpdateManagedUserRequest $request, int $listener): RedirectResponse
    {
        $user = $this->managedUsers->find($listener, UserTypeEnum::Listener);
        $this->managedUsers->update($user, $request->editableAttributes());

        return redirect()->route('listeners.index')->with('status', 'Listener updated successfully.');
    }

    public function toggleStatus(int $listener): RedirectResponse
    {
        $user = $this->managedUsers->find($listener, UserTypeEnum::Listener);
        $this->managedUsers->toggleStatus($user);

        return back()->with('status', 'Listener status updated successfully.');
    }

    public function destroy(int $listener): RedirectResponse
    {
        $user = $this->managedUsers->find($listener, UserTypeEnum::Listener);
        $this->managedUsers->softDelete($user);

        return back()->with('status', 'Listener deleted successfully.');
    }
}
