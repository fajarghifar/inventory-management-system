<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\DTOs\UserData;
use App\Services\UserService;
use Illuminate\Validation\Rule;

class UserForm extends Component
{
    public ?User $user = null;
    public bool $isEditing = false;

    public $name;
    public $username;
    public $email;
    public $password;
    public $password_confirmation;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->user?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'password' => [$this->isEditing ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }

    #[On('open-modal')]
    public function handleOpenModal($name): void
    {
        if ($name === 'user-form-modal' && !$this->isEditing) {
            $this->create(); // Ensure we reset if opening for create
        }
    }

    public function create(): void
    {
        $this->reset(['user', 'isEditing', 'name', 'username', 'email', 'password', 'password_confirmation']);
        $this->dispatch('open-modal', name: 'user-form-modal');
    }

    #[On('edit-user')]
    public function edit(User $user): void
    {
        $this->user = $user;
        $this->isEditing = true;

        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';

        $this->dispatch('open-modal', name: 'user-form-modal');
    }

    public function save(UserService $service): void
    {
        $this->validate();

        $data = new UserData(
            name: $this->name,
            username: $this->username,
            email: $this->email,
            password: $this->password ?: null, // Pass null if empty in edit mode
        );

        try {
            if ($this->isEditing && $this->user) {
                $service->updateUser($this->user, $data);
                $message = 'User updated successfully.';
            } else {
                $service->createUser($data);
                $message = 'User created successfully.';
            }

            $this->dispatch('close-modal', name: 'user-form-modal');
            $this->dispatch('pg:eventRefresh-user-table');
            $this->dispatch('toast', message: $message, type: 'success');

            // Reset after save
            $this->reset(['user', 'isEditing', 'name', 'username', 'email', 'password', 'password_confirmation']);

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.users.user-form');
    }
}
