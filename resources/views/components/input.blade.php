@props([
    'label',
    'model',
    'placeholder' => null
])

<div class="mb-3">
    <label class="small mb-1" for="{{ $model }}">
        {{ __($label) }}
        <span class="text-danger">*</span>
    </label>

    <input type="text"
           id="{{ $model }}"
           name="{{ $model }}"
           placeholder="{{ $placeholder }}"
           class="form-control form-control-solid @error($model) is-invalid @enderror"
    />

    @error($model)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{---
<div class="mb-3">
    <label class="small mb-1" for="name">
        {{ __('Name') }}
        <span class="text-danger">*</span>
    </label>

    <input type="text"
           id="name"
           name="name"
           wire:model.blur="name"
           wire:keyup="selectedName"
           placeholder="Enter name"
           class="form-control form-control-solid @error('name') is-invalid @enderror"
    />

    @error('name')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
---}}
