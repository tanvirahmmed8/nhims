@extends('layouts.app')

@section('title', 'Patient registration | NHIMS')

@section('content')
    <div class="nhims-auth-page">
        <div class="container py-4 py-lg-5">
            <div class="row justify-content-center g-4 g-xl-5">
                <div class="col-lg-5 col-xl-4">
                    <div class="nhims-auth-aside nhims-auth-aside-patient">
                        <span class="nhims-eyebrow">Patient self-service</span>
                        <h1>Create your NHIMS account.</h1>
                        <p>Public registration is designed for patients who need access to their own health information workspace.</p>
                        <div class="nhims-auth-trust-list">
                            <div><i class="bi bi-person-vcard"></i><span><strong>Your own profile</strong><small>Patient records stay linked to your account.</small></span></div>
                            <div><i class="bi bi-calendar2-check"></i><span><strong>Appointments</strong><small>Review upcoming visits and statuses.</small></span></div>
                            <div><i class="bi bi-file-medical"></i><span><strong>Published results</strong><small>See prescriptions and released lab results.</small></span></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-xl-6">
                    <div class="nhims-auth-card">
                        <div class="mb-4"><span class="nhims-auth-card-icon nhims-auth-card-icon-teal"><i class="bi bi-person-plus"></i></span><h2 class="h3 mt-3 mb-1">Patient registration</h2><p class="text-secondary mb-0">Create a secure account for patient self-service.</p></div>
                        <div class="alert alert-info d-flex gap-2 align-items-start" role="note"><i class="bi bi-info-circle-fill mt-1"></i><span><strong>Who can register here?</strong><br>Patients only. Staff, doctors, laboratory, pharmacy, and administrator accounts are created and assigned by an NHIMS administrator.</span></div>
                        <p class="small text-secondary mb-4"><i class="bi bi-link-45deg me-1"></i>In the demonstration workflow, an authorized receptionist links the patient account to the full patient profile before portal access is enabled.</p>
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3"><label for="name" class="form-label">Full name</label><input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="mb-3"><label for="email" class="form-label">Email address</label><input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="row g-3"><div class="col-md-6"><label for="password" class="form-label">Password</label><input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6"><label for="password-confirm" class="form-label">Confirm password</label><input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password"></div></div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">Create patient account <i class="bi bi-arrow-right ms-2"></i></button>
                        </form>
                        <div class="nhims-auth-footer mt-4 pt-3 border-top"><span>Already registered?</span><a href="{{ route('login') }}">Sign in</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
