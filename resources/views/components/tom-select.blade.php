@pushonce('page-styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
@endpushonce

@props([
    'label' => '',
    'name' => $name,
    'id' => '' ?? $name,
    'placeholder' => '',
    'data',
    'value'
])

<div class="col-md-4">
    <label for="{{ $id }}" class="form-label required" >
        {{ $label }}
    </label>

    <select id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}" autocomplete="off"
            class="form-control form-select @error($name) is-invalid @enderror"
    >
        <option value="">
            Select a person...
        </option>

        @foreach($data as $option)
            <option value="{{ $option->id }}" @selected(old($name, $value = '' ?? null) == $option->id)>
                {{ $option->name }}
            </option>
        @endforeach
    </select>

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

@pushonce('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        new TomSelect("#{{ $id }}",{
            create: true,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    </script>
@endpushonce

{{--- ---}}
{{---
<div class="col-md-4">
    <label class="small my-1" for="supplier_id">
        {{ __('Supplier') }}
        <span class="text-danger">*</span>
    </label>

    <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
        <option selected disabled>
            {{ __('Select a supplier:') }}
        </option>

        @foreach ($suppliers as $supplier)
            <option value="{{ $supplier->id }}" @selected(old('supplier_id', ) == $supplier->id)>
                {{ $supplier->name }}
            </option>
        @endforeach
    </select>

    @error('supplier_id')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
---}}
