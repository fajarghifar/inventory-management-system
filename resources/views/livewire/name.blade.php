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
