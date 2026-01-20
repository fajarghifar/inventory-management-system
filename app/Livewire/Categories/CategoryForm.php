<?php

namespace App\Livewire\Categories;

use Exception;
use Livewire\Component;
use App\Models\Category;
use App\DTOs\CategoryData;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use App\Services\CategoryService;

class CategoryForm extends Component
{
    public ?Category $category = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public bool $isEditing = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:categories,slug,' . ($this->category?->id)],
            'description' => ['nullable', 'string'],
        ];
    }

    public function updatedName(): void
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
    public function create(): void
    {
        $this->reset();
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'category-modal');
    }

    #[On('edit-category')]
    public function edit(Category $category): void
    {
        $this->resetValidation();
        $this->category = $category;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'category-modal');
    }

    public function save(CategoryService $service): void
    {
        $validated = $this->validate($this->rules());

        try {
            $categoryData = CategoryData::fromArray($validated);

            if ($this->isEditing && $this->category) {
                $service->updateCategory($this->category, $categoryData);
                $message = 'Category updated successfully.';
            } else {
                $service->createCategory($categoryData);
                $message = 'Category created successfully.';
            }

            $this->dispatch('close-modal', name: 'category-modal');
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('pg:eventRefresh-category-table');
            $this->dispatch('toast', message: $message, type: 'success');
            $this->reset();

        } catch (Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
