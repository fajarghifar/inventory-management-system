@extends('dashboard.body.main')

@section('specificpagescripts')
<script src="{{ asset('assets/js/img-preview.js') }}"></script>
@endsection

@section('content')
<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
    <div class="container-xl px-4">
        <div class="page-header-content">
            <div class="row align-items-center justify-content-between pt-3">
                <div class="col-auto mb-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i data-feather="user"></i></div>
                        Account Settings - Settings
                    </h1>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- BEGIN: Main page content -->
<div class="container-xl px-4 mt-4">
    <!-- Account page navigation -->
    <nav class="nav nav-borders">
        <a class="nav-link ms-0" href="{{ route('profile.edit') }}">Profile</a>
        <a class="nav-link active" href="{{ route('profile.settings') }}">Settings</a>
    </nav>

    <hr class="mt-0 mb-4" />

    <!-- BEGIN: Alert -->
    @if (session()->has('success'))
    <div class="alert alert-success alert-icon" role="alert">
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-icon-aside">
            <i class="far fa-flag"></i>
        </div>
        <div class="alert-icon-content">
            {{ session('success') }}
        </div>
    </div>
    @endif
    <!-- END: Alert -->

    <!-- BEGIN: FORM -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Change password card-->
            <div class="card mb-4">
                <div class="card-header">Change Password</div>
                <div class="card-body">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('put')
                        <!-- Form Group (current password)-->
                        <div class="mb-3">
                            <label class="small mb-1" for="current_password">Current Password <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('current_password') is-invalid @enderror" id="current_password" name="current_password" type="password" placeholder="" />
                            @error('current_password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Group (new password)-->
                        <div class="mb-3">
                            <label class="small mb-1" for="password">New Password <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="" />
                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Group (confirm password)-->
                        <div class="mb-3">
                            <label class="small mb-1" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <input class="form-control form-control-solid @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" type="password" placeholder="" />
                            @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- Two factor authentication card-->
            <div class="card mb-4">
                <div class="card-header">Two-Factor Authentication</div>
                <div class="card-body">
                    <p>Add another level of security to your account by enabling two-factor authentication. We will send you a text message to verify your login attempts on unrecognized devices and browsers.</p>
                    <form>
                        <div class="form-check">
                            <input class="form-check-input" id="twoFactorOn" type="radio" name="twoFactor" checked="" />
                            <label class="form-check-label" for="twoFactorOn">On</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" id="twoFactorOff" type="radio" name="twoFactor" />
                            <label class="form-check-label" for="twoFactorOff">Off</label>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Delete account card-->
            <div class="card mb-4">
                <div class="card-header">Delete Account</div>
                <div class="card-body">
                    <p>Deleting your account is a permanent action and cannot be undone. If you are sure you want to delete your account, select the button below.</p>
                    <button class="btn btn-danger-soft text-danger" type="button">I understand, delete my account</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END: FORM -->
</div>
<!-- END: Main page content -->
@endsection
