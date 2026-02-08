<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class UserDetail extends Component
{
    public ?User $user = null;

    #[On('view-user')]
    public function show(User $user): void
    {
        $this->user = $user;
        $this->dispatch('open-modal', name: 'user-detail-modal');
    }

    public function render()
    {
        return view('livewire.users.user-detail');
    }
}
