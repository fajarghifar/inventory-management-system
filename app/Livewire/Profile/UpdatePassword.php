<?php

namespace App\Livewire\Profile;

use App\Models\User;
use App\Services\ProfileService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UpdatePassword extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function updatePassword(ProfileService $service): void
    {
        $this->validate();

        try {
            /** @var User $user */
            $user = Auth::user();

            $service->updatePassword($user, $this->current_password, $this->password);

            $this->reset(['current_password', 'password', 'password_confirmation']);
            $this->dispatch('password-updated');
            $this->dispatch('toast', message: 'Password updated successfully.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.profile.update-password');
    }
}
