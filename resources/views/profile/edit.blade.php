@extends('layouts.tabler')

@section('content')
<div class="container-xl px-4 mt-4">
    <x-alert/>

    <nav class="nav nav-borders">
        <a class="nav-link active ms-0" href="{{ route('profile.edit') }}">Profile</a>
        <a class="nav-link" href="{{ route('profile.settings') }}">Settings</a>
    </nav>

    <hr class="mt-0 mb-4" />

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div class="row">

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">
                            {{ __('Profile Image') }}
                        </h3>

                        <img
                            class="img-account-profile mb-2"
                            src="{{ $user->photo ? asset('storage/profile/'.$user->photo) : asset('assets/img/demo/user-placeholder.svg') }}"
                            id="image-preview"
                        />

                        <div class="small font-italic text-muted mb-2">JPG or PNG no larger than 1 MB</div>

                        <input class="form-control @error('photo') is-invalid @enderror" type="file"  id="image" name="photo" accept="image/*" onchange="previewImage();">

                        @error('photo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">
                            {{ __('Supplier Details') }}
                        </h3>

                        <x-input name="name" value="{{ old('name', $user->name) }}" :required="true" />

                        <x-input name="email" label="Email address" value="{{ old('email', $user->email) }}"  :required="true" />

                        <x-input name="username" value="{{ old('username', $user->username) }}" :required="true" />
                    </div>

                    <div class="card-footer text-end">
                        <x-button.save type="submit">
                            {{ __('Update') }}
                        </x-button.save>

                        <x-button.back route="{{ route('dashboard') }}">
                            {{ __('Cancel') }}
                        </x-button.back>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('page-scripts')
    <script src="{{ asset('assets/js/img-preview.js') }}"></script>
@endpush
