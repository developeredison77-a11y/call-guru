<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill([
            'name' => $data['name'],
            'country_code' => $data['country_code'],
            'mobile_number' => $data['mobile_number'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'sex' => $data['sex'] ?? null,
        ]);

        $user->save();

        return back()->with('status', 'Profile updated successfully.');
    }
}
