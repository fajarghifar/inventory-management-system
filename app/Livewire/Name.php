<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;

class Name extends Component
{
    #[Rule('required')]
    public $name = '';

    public function render()
    {
        return view('livewire.name');
    }

    public function selectedName(): void
    {
        $this->dispatch('name-selected', selectedName: $this->name)
            ->to(Slug::class);
    }
}
