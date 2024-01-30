@extends('layouts.tabler')

@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="user"></i></div>
                            Account Settings - Profile
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-xl px-4 mt-4">
        @include('profile.component.menu')

        <hr class="mt-0 mb-4" />

        @include('partials.session')

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-xl-4">
                    <!-- Profile picture card -->
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header">Profile Picture</div>
                        <div class="card-body text-center">
                            <!-- Profile picture image -->
                            <img class="img-account-profile rounded-circle mb-2"
                                src="{{ $user->photo ? asset('storage/profile//' . $user->photo) : asset('assets/img/illustrations/profiles/profile-1.png') }}"
                                alt="" id="image-preview" />
                            <!-- Profile picture help block -->
                            <div class="small font-italic text-muted mb-2">JPG or PNG no larger than 1 MB</div>
                            <!-- Profile picture input -->
                            <input class="form-control form-control-solid mb-2 @error('photo') is-invalid @enderror"
                                type="file" id="image" name="photo" accept="image/*" onchange="previewImage();">
                            @error('photo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <!-- Account details card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            Account Details
                        </div>
                        <div class="card-body">
                            <!-- Form Group (username) -->
                            <div class="mb-3">
                                <label class="small mb-1" for="username">Username</label>
                                <input class="form-control form-control-solid @error('username') is-invalid @enderror"
                                    id="username" name="username" type="text" placeholder=""
                                    value="{{ old('username', $user->username) }}" autocomplete="off" />
                                @error('username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Form Group (name) -->
                            <div class="mb-3">
                                <label class="small mb-1" for="name">Full name</label>
                                <input class="form-control form-control-solid @error('name') is-invalid @enderror"
                                    id="name" name="name" type="text" placeholder=""
                                    value="{{ old('name', $user->name) }}" autocomplete="off" />
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="email">
                                    Email address
                                </label>

                                <input class="form-control form-control-solid @error('photo') is-invalid @enderror"
                                    id="email" name="email" type="text" placeholder=""
                                    value="{{ old('email', $user->email) }}" autocomplete="off" />
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button class="btn btn-primary" type="submit">
                                {{ __('Update') }}
                            </button>
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
