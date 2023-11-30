<div class="mb-3">
    <label for="name" class="form-label required">
        {{ __('Name') }}
    </label>

    <input type="text"
           id="name"
           name="name"
           wire:model.blur="name"
           wire:keyup="selectedName"
           placeholder="Enter name"
           class="form-control @error('name') is-invalid @enderror"
    />

    @error('name')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
