<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private ImageService $images) {}

    /** Display the user's profile form. */
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    /** Update the user's profile information. */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->only(['username', 'full_name']);

        if ($request->hasFile('avatar')) {
            $this->images->delete($user->avatar);
            $data['avatar'] = $this->images->store($request->file('avatar'), 'avatars', 256);
        } elseif ($request->boolean('remove_avatar')) {
            $this->images->delete($user->avatar);
            $data['avatar'] = null;
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }
}
