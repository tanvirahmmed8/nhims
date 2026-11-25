@extends('layouts.app')

@section('title', 'Sign in | NHIMS')

@section('content')
    <div class="nhims-auth-page">
        <div class="container py-4 py-lg-5">
            <div class="row justify-content-center g-4 g-xl-5">
                <div class="col-lg-5 col-xl-4">
                    <div class="nhims-auth-aside">
                        <span class="nhims-eyebrow">Secure care workspace</span>
                        <h1>Welcome back to NHIMS.</h1>
                        <p>Sign in to continue patient care, service coordination, and accountable health information workflows.</p>
                        <div class="nhims-auth-trust-list">
                            <div><i class="bi bi-shield-check"></i><span><strong>Role-aware access</strong><small>See the tools your role permits.</small></span></div>
                            <div><i class="bi bi-building-check"></i><span><strong>Facility context</strong><small>Work within assigned facilities.</small></span></div>
                            <div><i class="bi bi-journal-check"></i><span><strong>Audit-ready actions</strong><small>Sensitive access stays traceable.</small></span></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-xl-6">
                    <div class="nhims-auth-card">
                        <div class="mb-4">
                            <span class="nhims-auth-card-icon"><i class="bi bi-box-arrow-in-right"></i></span>
                            <h2 class="h3 mt-3 mb-1">Sign in</h2>
                            <p class="text-secondary mb-0">Use your NHIMS account to open the workspace.</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center"><label for="password" class="form-label">Password</label>@if (Route::has('password.request'))<a href="{{ route('password.request') }}" class="small">Forgot password?</a>@endif</div>
                                <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}><label class="form-check-label" for="remember">Remember me</label></div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">Sign in <i class="bi bi-arrow-right ms-2"></i></button>
                        </form>

                        @if (app()->environment(['local', 'development']) && config('app.debug'))
                            @php
                                $demoAccounts = [
                                    ['label' => 'Administrator', 'email' => 'admin@nhims.test', 'icon' => 'bi-shield-lock', 'class' => 'demo-admin'],
                                    ['label' => 'Receptionist', 'email' => 'receptionist@nhims.test', 'icon' => 'bi-person-plus', 'class' => 'demo-reception'],
                                    ['label' => 'Doctor', 'email' => 'doctor.dhaka@nhims.test', 'icon' => 'bi-clipboard2-pulse', 'class' => 'demo-doctor'],
                                    ['label' => 'Lab staff', 'email' => 'lab@nhims.test', 'icon' => 'bi-eyedropper', 'class' => 'demo-lab'],
                                    ['label' => 'Pharmacy', 'email' => 'pharmacy@nhims.test', 'icon' => 'bi-capsule', 'class' => 'demo-pharmacy'],
                                    ['label' => 'Patient', 'email' => 'patient@nhims.test', 'icon' => 'bi-person-vcard', 'class' => 'demo-patient'],
                                ];
                            @endphp
                            <div class="nhims-demo-login mt-4 pt-4 border-top">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3"><div><span class="nhims-demo-label"><i class="bi bi-code-slash me-1"></i>Local development</span><h3 class="h6 mt-2 mb-1">Quick demo login</h3><p class="small text-secondary mb-0">Click a role to sign in with the seeded demo account.</p></div><span class="badge text-bg-warning">DEV</span></div>
                                <div class="row g-2">
                                    @foreach ($demoAccounts as $account)
                                        <div class="col-6 col-md-4">
                                            <form method="POST" action="{{ route('login') }}">
                                                @csrf
                                                <input type="hidden" name="email" value="{{ $account['email'] }}">
                                                <input type="hidden" name="password" value="password">
                                                <button type="submit" class="nhims-demo-button {{ $account['class'] }}"><i class="bi {{ $account['icon'] }}"></i><span>{{ $account['label'] }}</span></button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="nhims-auth-footer mt-4 pt-3 border-top"><span>New patient?</span><a href="{{ route('register') }}">Create a patient account</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
