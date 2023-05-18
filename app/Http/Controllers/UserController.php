<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $users = User::filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('users.index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|max:50',
            'email' => 'required|email|max:50|unique:users,email',
            'username' => 'required|min:4|max:25|alpha_dash:ascii|unique:users,username',
            'password' => 'required_with:password_confirmation|min:6',
            'password_confirmation' => 'same:password|min:6',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['password'] = Hash::make($request->password);

        /**
         * Handle upload an image
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/profile/';

            /**
             * Store an image to Storage.
             */
            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        User::create($validatedData);

        return Redirect::route('users.index')->with('success', 'New User has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|max:50',
            'photo' => 'image|file|max:1024',
            'email' => 'required|email|max:50|unique:users,email,'.$user->id,
            'username' => 'required|min:4|max:25|alpha_dash:ascii|unique:users,username,'.$user->id,
        ];

        $validatedData = $request->validate($rules);

        if ($validatedData['email'] != $user->email) {
            $validatedData['email_verified_at'] = null;
        }

        /**
         * Handle upload image with Storage.
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/profile/';

            /**
             * Delete an image if exists.
             */
            if($user->photo){
                Storage::delete($path . $user->photo);
            }

            // Store an image to Storage
            $file->storeAs($path, $fileName);

            $validatedData['photo'] = $fileName;
        }

        User::where('id', $user->id)->update($validatedData);

        return Redirect::route('users.index')->with('success', 'User has been updated!');
    }

    public function updatePassword(Request $request, String $username)
    {
        # Validation
        $validated = $request->validate([
            'password' => 'required_with:password_confirmation|min:6',
            'password_confirmation' => 'same:password|min:6',
        ]);

        # Update the new Password
        User::where('username', $username)->update([
            'password' => Hash::make($validated['password'])
        ]);

        return Redirect::route('users.index')->with('success', 'User has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        /**
         * Delete photo if exists.
         */
        if($user->photo){
            Storage::delete('public/profile/' . $user->photo);
        }

        User::destroy($user->id);

        return Redirect::route('users.index')->with('success', 'User has been deleted!');
    }
}
