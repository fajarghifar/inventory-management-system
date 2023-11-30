<div class="mb-3">
    <label for="slug" class="form-label required">
        {{ __('Slug') }}
    </label>

    <input type="text"
           id="slug"
           name="slug"
           wire:model.blur="slug"
           placeholder="Enter slug"
           class="form-control @error('slug') is-invalid @enderror"
    />

    @error('slug')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
