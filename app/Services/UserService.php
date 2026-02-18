<?php

namespace App\Services;

use App\Models\User;
use App\DTOs\UserData;
use App\Models\Purchase;
use App\Models\FinanceTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function createUser(UserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data->name,
                'username' => $data->username,
                'email' => $data->email,
                'password' => Hash::make($data->password),
            ]);

            Cache::forget('users_list_all');

            return $user;
        });
    }

    public function updateUser(User $user, UserData $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data->name,
                'username' => $data->username,
                'email' => $data->email,
            ];

            if ($data->password) {
                $updateData['password'] = Hash::make($data->password);
            }

            $user->update($updateData);

            Cache::forget('users_list_all');

            return $user->fresh();
        });
    }

    public function deleteUser(User $user): void
    {
        if ($user->id === Auth::id()) {
            throw ValidationException::withMessages(['user' => 'You cannot delete your own account.']);
        }

        if ($user->sales()->exists()) {
            throw ValidationException::withMessages(['user' => 'Cannot delete user who has recorded sales.']);
        }

        if (Purchase::where('created_by', $user->id)->exists()) {
            throw ValidationException::withMessages(['user' => 'Cannot delete user who has recorded purchases.']);
        }

        if (FinanceTransaction::where('created_by', $user->id)->exists()) {
            throw ValidationException::withMessages(['user' => 'Cannot delete user who has recorded finance transactions.']);
        }

        $user->delete();

        Cache::forget('users_list_all');
    }
}
