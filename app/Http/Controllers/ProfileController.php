<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user()->fill($request->validated());

        $rules = [
            'name' => 'required|max:50',
            'photo' => 'image|file|max:1024',
            'email' => 'required|email|max:50|unique:users,email,'.$user->id,
            'username' => 'required|min:4|max:25|alpha_dash:ascii|unique:users,username,'.$user->id
        ];

        $validatedData = $request->validate($rules);

        if ($validatedData['email'] != $user->email)
        {
            $validatedData['email_verified_at'] = null;
        }

        /**
         * Handle upload image
         */
        if ($file = $request->file('photo'))
        {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/profile/';

            /**
             * Delete an image if exists.
             */
            if($user->photo)
            {
                Storage::delete($path . $user->photo);
            }

            /**
             * Store an image to Storage.
             */
            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        User::where('id', $user->id)->update($validatedData);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile has been updated!');
    }

    public function settings(Request $request)
    {
        return view('profile.settings', [
            'user' => $request->user(),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->to('/');
    }
}
