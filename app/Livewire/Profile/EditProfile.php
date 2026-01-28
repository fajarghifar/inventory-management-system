<?php

namespace App\Livewire\Profile;

use App\Models\User;
use App\Services\ProfileService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EditProfile extends Component
{
    public string $name = '';
    public string $username = '';
    public string $email = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . Auth::id()],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ];
    }

    public function updateProfile(ProfileService $service): void
    {
        $this->validate();

        try {
            /** @var User $user */
            $user = Auth::user();

            $service->updateProfile($user, [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
            ]);

            $this->dispatch('profile-updated', name: $user->name);
            $this->dispatch('toast', message: 'Profile updated successfully.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.profile.edit-profile');
    }
}
