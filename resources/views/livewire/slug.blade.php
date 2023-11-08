<div class="mb-3">
    <label class="small mb-1" for="slug">
        {{ __('Slug') }}
    </label>

    <input type="text"
           id="slug"
           name="slug"
           wire:model.blur="slug"
           placeholder="Enter slug"
           class="form-control form-control-solid @error('slug') is-invalid @enderror"
    />

    @error('slug')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
