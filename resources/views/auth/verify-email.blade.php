@extends('layouts.auth')

@section('content')
<div class="text-center">
    <div class="my-5">
        <p class="fs-h3 text-secondary">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>
</div>


<form action="{{ route('verification.send') }}" method="POST" autocomplete="off">
    @csrf

    <button type="submit" class="btn btn-primary w-100">
        {{ __('Resend Verification Email') }}
    </button>

    <div class="mt-4">
        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    </div>
                    <div>
                        <div class="text-secondary">
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</form>

<form action="{{ route('logout') }}" method="POST" autocomplete="off">
    @csrf

    <div class="form-footer">
        <button type="submit" class="btn btn-primary w-100">
            {{ __('Log Out') }}
        </button>
    </div>
</form>
@endsection
