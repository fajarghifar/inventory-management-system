@extends('auth.body.main')

@section('content')
<div class="container-xl px-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-9">
            <!-- BEGIN: Social Registration Form -->
            <div class="card my-5">
                <div class="card-body p-5 text-center">
                    <div class="h3 fw-light mb-3">Create an Account</div>
                    <div class="small text-muted mb-2">Sign in using...</div>
                    <!-- BEGIN: Social Registration Links -->
                    <a class="btn btn-icon btn-facebook mx-1" href="#"><i class="fab fa-facebook-f fa-fw fa-sm"></i></a>
                    <a class="btn btn-icon btn-github mx-1" href="#"><i class="fab fa-github fa-fw fa-sm"></i></a>
                    <a class="btn btn-icon btn-google mx-1" href="#"><i class="fab fa-google fa-fw fa-sm"></i></a>
                    <a class="btn btn-icon btn-twitter mx-1" href="#"><i class="fab fa-twitter fa-fw fa-sm text-white"></i></a>
                    <!-- END: Social Registration Links -->
                </div>

                <hr class="my-0" />

                <div class="card-body p-5">
                    <div class="text-center small text-muted mb-4">...or enter your information below.</div>
                    <!-- BEGIN: Login Form -->
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <!-- Form Group (name) -->
                        <div class="mb-3">
                            <label class="text-gray-600 small" for="name">Full Name</label>
                            <input class="form-control form-control-solid @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="" value="{{ old('name') }}" autocomplete="off"/>
                            @error('name')
                            <div class="invalid-feedback">
                                <i class="bx bx-radio-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Group (usename) -->
                        <div class="mb-3">
                            <label class="text-gray-600 small" for="username">Username</label>
                            <input class="form-control form-control-solid @error('username') is-invalid @enderror" type="text" id="username" name="username" placeholder="" value="{{ old('username') }}" autocomplete="off"/>
                            @error('username')
                            <div class="invalid-feedback">
                                <i class="bx bx-radio-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Group (email address) -->
                        <div class="mb-3">
                            <label class="text-gray-600 small" for="email">Email Address</label>
                            <input class="form-control form-control-solid @error('email') is-invalid @enderror" type="text" id="email" name="email" placeholder=""  value="{{ old('email') }}" autocomplete="off"/>
                            @error('email')
                            <div class="invalid-feedback">
                                <i class="bx bx-radio-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3">
                            <div class="col-md-6">
                                <!-- Form Group (choose password) -->
                                <div class="mb-3">
                                    <label class="text-gray-600 small" for="password">Password</label>
                                    <input class="form-control form-control-solid @error('password') is-invalid @enderror" type="password" id="password" name="password" placeholder=""/>
                                    @error('password')
                                    <div class="invalid-feedback">
                                        <i class="bx bx-radio-circle"></i>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Form Group (confirm password) -->
                                <div class="mb-3">
                                    <label class="text-gray-600 small" for="password_confirmation">Confirm Password</label>
                                    <input class="form-control form-control-solid @error('password') is-invalid @enderror" type="password" id="password_confirmation" name="password_confirmation" placeholder=""/>
                                </div>
                            </div>
                        </div>
                        <!-- Form Group (form submission) -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" id="checkTerms" type="checkbox" value="" />
                                <label class="form-check-label" for="checkTerms">
                                    I accept the <a href="#">terms &amp; conditions</a>.
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </div>
                    </form>
                    <!-- END: Login Form -->
                </div>
                <hr class="my-0" />
                <div class="card-body px-5 py-4">
                    <div class="small text-center">
                        Have an account?
                        <a href="{{ route('login') }}">Sign in!</a>
                    </div>
                </div>
            </div>
            <!-- END: Social Registration Form -->
        </div>
    </div>
</div>
@endsection
