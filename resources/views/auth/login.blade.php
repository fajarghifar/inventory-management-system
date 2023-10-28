@extends('layouts.auth')

@section('content')
<div class="container-xl px-4">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11">
            <div class="card my-5">
                <div class="card-body p-5 text-center">
                    <div class="h3 fw-light mb-3">Sign In</div>
                    <!-- BEGIN: Social Login Links-->
                    <a class="btn btn-icon btn-facebook mx-1" href="#"><i class="fab fa-facebook-f fa-fw fa-sm"></i></a>
                    <a class="btn btn-icon btn-github mx-1" href="#"><i class="fab fa-github fa-fw fa-sm"></i></a>
                    <a class="btn btn-icon btn-google mx-1" href="#"><i class="fab fa-google fa-fw fa-sm"></i></a>
                    <a class="btn btn-icon btn-twitter mx-1" href="#"><i class="fab fa-twitter fa-fw fa-sm text-white"></i></a>
                    <!-- END: Social Login Links-->
                </div>
                <hr class="my-0" />
                <div class="card-body p-5">
                    <!-- BEGIN: Login Form-->
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <!-- Form Group (email address)-->
                        <div class="mb-3">
                            <label class="text-gray-600 small" for="input_type">Email / Username</label>
                            <input
                                class="form-control form-control-solid @if($errors->get('email') OR $errors->get('username')) is-invalid @endif"
                                type="text"
                                id="input_type"
                                name="input_type"
                                placeholder=""
                                value="{{ old('input_type') }}"
                                autocomplete="off"
                            />
                            @if ($errors->get('email') OR $errors->get('username'))
                                <div class="invalid-feedback">
                                    Incorrect username or password.
                                </div>
                            @endif
                        </div>
                        <!-- Form Group (password)-->
                        <div class="mb-3">
                            <label class="text-gray-600 small" for="password">Password</label>
                            <input
                                class="form-control form-control-solid @if($errors->get('email') OR $errors->get('username')) is-invalid @endif @error('password') is-invalid @enderror"
                                type="password"
                                id="password" name="password"
                                placeholder=""
                            />
                            @error('password')
                            <div class="invalid-feedback">
                                <i class="bx bx-radio-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Form Group (forgot password link)-->
                        <div class="mb-3"><a class="small" href="#">Forgot your password?</a></div>
                        <!-- Form Group (login box)-->
                        <div class="d-flex align-items-center justify-content-between mb-0">
                            <div class="form-check">
                                <input class="form-check-input" id="remember_me" name="remember" type="checkbox" />
                                <label class="form-check-label" for="remember_me">Remember me.</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    <!-- END: Login Form-->
                </div>
                <hr class="my-0" />
                <div class="card-body px-5 py-4">
                    <div class="small text-center">
                        New user?
                        <a href="{{ route('register') }}">Create an account!</a>
                    </div>
                </div>
            </div>
            <!-- END: Social Login Form-->
        </div>
    </div>
</div>
@endsection
