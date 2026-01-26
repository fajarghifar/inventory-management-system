<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\On;

class CategoryDetail extends Component
{
    public ?Category $category = null;

    public function render()
    {
        return view('livewire.categories.category-detail');
    }

    #[On('show-category')]
    public function show(Category $category)
    {
        $this->category = $category;
        $this->dispatch('open-modal', name: 'category-detail-modal');
    }

    public function closeModal()
    {
        $this->category = null;
    }
}
