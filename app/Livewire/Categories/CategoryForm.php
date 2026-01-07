<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use App\Services\CategoryService;

class CategoryForm extends Component
{
    public ?Category $category = null;

    #[Rule('required|string|max:100')]
    public string $name = '';

    #[Rule('nullable|string|max:150|unique:categories,slug,except,id')]
    public string $slug = '';

    #[Rule('nullable|string')]
    public string $description = '';

    public bool $isEditing = false;

    public function updatedName()
    {
        // Auto-generate slug when name changes, if not editing an existing slug manually
        if (!$this->isEditing || empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function render()
    {
        return view('livewire.categories.category-form');
    }

    #[On('create-category')]
    public function create()
    {
        $this->reset();
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'category-modal');
    }

    #[On('edit-category')]
    public function edit(Category $category)
    {
        $this->resetValidation();
        $this->category = $category;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'category-modal');
    }

    public function save(CategoryService $service)
    {
        $validated = $this->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:150|unique:categories,slug,' . ($this->category?->id),
            'description' => 'nullable|string',
        ]);

        try {
            if ($this->isEditing && $this->category) {
                $service->updateCategory($this->category, $validated);
                $message = 'Category updated successfully.';
            } else {
                $service->createCategory($validated);
                $message = 'Category created successfully.';
            }

            $this->dispatch('close-modal', name: 'category-modal');
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('pg:eventRefresh-category-table');
            $this->dispatch('toast', message: $message, type: 'success');
            $this->reset();

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
