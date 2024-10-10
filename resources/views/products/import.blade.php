@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">

        <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <x-card>
                <x-slot:header>
                    <x-slot:title>
                        {{ __('Import Products') }}
                    </x-slot:title>

                    <x-slot:actions>
                        <x-action.close route="{{ route('products.index') }}" />
                    </x-slot:actions>
                </x-slot:header>

                <x-slot:content>
                    <input type="file"
                           id="file"
                           name="file"
                           class="form-control @error('file') is-invalid @enderror"
                           accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                    >

                    @error('file')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </x-slot:content>

                <x-slot:footer class="text-end">
                    <x-button type="submit">
                        {{ __('Import') }}
                    </x-button>

                    <x-button.back route="{{ route('products.index') }}">
                        {{ __('Cancel') }}
                    </x-button.back>
                </x-slot:footer>
            </x-card>
        </form>
    </div>
</div>
@endsection

@pushonce('page-scripts')
    <script src="{{ asset('assets/js/img-preview.js') }}"></script>
@endpushonce
