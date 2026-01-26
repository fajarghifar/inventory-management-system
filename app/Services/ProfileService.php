<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class ProfileService
{
    /**
     * Update user profile information.
     *
     * @param User $user
     * @param array{name: string, email: string} $data
     * @return User
     * @throws Exception
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            try {
                // Ensure email uniqueness allows for the current user
                // Validation at the request layer should catch this, but double safety here.
                if (
                    $user->email !== $data['email'] &&
                    User::where('email', $data['email'])->exists()
                ) {
                    throw new Exception('The email address is already in use by another account.');
                }

                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);

                Log::info("User profile updated: {$user->id}");

                return $user;

            } catch (Exception $e) {
                Log::error("Failed to update profile for user {$user->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Update user password.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return void
     * @throws Exception
     */
    public function updatePassword(User $user, string $currentPassword, string $newPassword): void
    {
        DB::transaction(function () use ($user, $currentPassword, $newPassword) {
            try {
                if (!Hash::check($currentPassword, $user->password)) {
                    throw new Exception('The provided current password does not match your password.');
                }

                $user->update([
                    'password' => Hash::make($newPassword),
                ]);

                Log::info("User password updated: {$user->id}");

            } catch (Exception $e) {
                Log::error("Failed to update password for user {$user->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }
}
