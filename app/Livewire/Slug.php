<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

class Slug extends Component
{
    #[Rule('required')]
    public $slug = '';

    public function render()
    {
        return view('livewire.slug');
    }

    #[On('name-selected')]
    public function generateSlug($selectedName): void
    {
        $this->slug = Str::slug($selectedName, '-');
    }
}
