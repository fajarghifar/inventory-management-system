<?php

namespace App\Services;

use App\Models\User;
use App\DTOs\UserData;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function createUser(UserData $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::create([
                'name' => $data->name,
                'username' => $data->username,
                'email' => $data->email,
                'password' => Hash::make($data->password),
            ]);
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

            return $user->fresh();
        });
    }

    public function deleteUser(User $user): void
    {
        // Prevent deleting self
        if ($user->id === Auth::id()) {
            throw ValidationException::withMessages(['user' => 'You cannot delete your own account.']);
        }

        // Prevent deleting Super Admin if implemented, but for now just self check
        // Check relationships (sales, purchases, etc) if necessary
        // Ideally we should soft delete or just block, but the user asked for delete.
        // I'll add a check if they have sales/transactions to prevent orphan records if needed,
        // but User model has `sales()` relation.

        if ($user->sales()->exists()) {
            throw ValidationException::withMessages(['user' => 'Cannot delete user who has recorded sales.']);
        }

        // Check for other relations if they exist (finance transactions, purchases)
        // Assuming Purchases also have created_by
        if (Purchase::where('created_by', $user->id)->exists()) {
            throw ValidationException::withMessages(['user' => 'Cannot delete user who has recorded purchases.']);
        }

        if (FinanceTransaction::where('created_by', $user->id)->exists()) {
            throw ValidationException::withMessages(['user' => 'Cannot delete user who has recorded finance transactions.']);
        }

        $user->delete();
    }
}
