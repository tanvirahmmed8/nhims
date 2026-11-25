<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'NHIMS'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="nhims-body">
    @auth
        @php
            $currentUser = Auth::user();
            $primaryFacility = $currentUser->facilities()->wherePivot('is_primary', true)->first();
            $userRole = $currentUser->role?->name ?? 'Team member';
        @endphp

        <div class="nhims-shell">
            <aside class="nhims-sidebar" aria-label="Primary navigation">
                <button type="button" class="nhims-sidebar-close" data-nhims-sidebar-close aria-label="Close navigation">
                    <i class="bi bi-x-lg"></i>
                </button>

                <a href="{{ url('/home') }}" class="nhims-brand">
                    <span class="nhims-brand-mark" aria-hidden="true"><i class="bi bi-heart-pulse"></i></span>
                    <span>
                        <span class="nhims-brand-title">NHIMS</span>
                        <span class="nhims-brand-subtitle">Health information</span>
                    </span>
                </a>

                <nav class="nav flex-column" aria-label="Application">
                    <div class="nhims-nav-label">Workspace</div>
                    <a class="nav-link active" href="{{ url('/home') }}">
                        <i class="bi bi-grid-1x2-fill" aria-hidden="true"></i>
                        <span>Overview</span>
                    </a>
                    @if ($currentUser->hasRole('patient'))
                        <a class="nav-link" href="{{ route('portal.index') }}"><i class="bi bi-person-vcard" aria-hidden="true"></i><span>My health portal</span></a>
                    @endif
                    @if ($currentUser->hasAnyRole(['admin', 'receptionist', 'doctor', 'patient']))
                        <a class="nav-link" href="{{ route('patients.index') }}"><i class="bi bi-people" aria-hidden="true"></i><span>Patients</span></a>
                    @endif
                    @if ($currentUser->hasAnyRole(['admin', 'receptionist', 'doctor', 'patient']))
                        <a class="nav-link" href="{{ route('appointments.index') }}"><i class="bi bi-calendar2-week" aria-hidden="true"></i><span>Appointments</span></a>
                    @endif

                    <div class="nhims-nav-label">Care delivery</div>
                    @if ($currentUser->hasRole('doctor'))
                        <a class="nav-link" href="{{ route('consultations.index') }}"><i class="bi bi-clipboard2-pulse" aria-hidden="true"></i><span>Consultations</span></a>
                    @endif
                    @if ($currentUser->hasAnyRole(['doctor', 'pharmacy_staff', 'patient']))
                        <a class="nav-link" href="{{ route('prescriptions.index') }}"><i class="bi bi-file-medical" aria-hidden="true"></i><span>Prescriptions</span></a>
                    @endif
                    @if ($currentUser->hasAnyRole(['doctor', 'lab_staff', 'patient']))
                        <a class="nav-link" href="{{ route('laboratory.index') }}"><i class="bi bi-eyedropper" aria-hidden="true"></i><span>Laboratory</span></a>
                    @endif
                    @if ($currentUser->hasRole('pharmacy_staff'))
                        <a class="nav-link" href="{{ route('pharmacy.index') }}"><i class="bi bi-capsule" aria-hidden="true"></i><span>Pharmacy</span></a>
                    @endif

                    @if ($currentUser->hasRole('admin'))
                        <div class="nhims-nav-label">Administration</div>
                        <a class="nav-link" href="{{ route('admin.facilities.index') }}"><i class="bi bi-building" aria-hidden="true"></i><span>Facilities</span></a>
                        <a class="nav-link" href="{{ route('admin.staff.index') }}"><i class="bi bi-person-gear" aria-hidden="true"></i><span>Staff</span></a>
                        <a class="nav-link" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart-line" aria-hidden="true"></i><span>Reports</span></a>
                        <a class="nav-link" href="{{ route('admin.audit.index') }}"><i class="bi bi-shield-check" aria-hidden="true"></i><span>Audit logs</span></a>
                    @endif
                </nav>

                <div class="nhims-sidebar-footer">
                    <small class="d-block mb-2">Current facility</small>
                    <div class="small fw-semibold text-white">{{ $primaryFacility?->name ?? 'Facility not assigned' }}</div>
                    <div class="small mt-1">{{ $userRole }}</div>
                </div>
            </aside>

            <div class="nhims-main">
                <header class="nhims-topbar">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-light nhims-menu-toggle" data-nhims-sidebar-open aria-label="Open navigation">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <div>
                            <p class="nhims-topbar-title">@yield('page_title', 'Overview')</p>
                            <p class="nhims-topbar-context">{{ $primaryFacility?->name ?? 'National workspace' }}</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="nhims-topbar-search position-relative d-none d-md-block">
                            <i class="bi bi-search" aria-hidden="true"></i>
                            <input type="search" class="form-control" placeholder="Search patients, records..." aria-label="Search patients and records">
                        </div>
                        <button type="button" class="btn btn-light position-relative" aria-label="Notifications">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"><span class="visually-hidden">Unread notification</span></span>
                        </button>
                        <div class="dropdown">
                            <button class="btn d-flex align-items-center gap-2 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="nhims-avatar">{{ strtoupper(substr($currentUser->name, 0, 1)) }}</span>
                                <span class="d-none d-lg-block text-start">
                                    <span class="d-block small fw-semibold text-dark">{{ $currentUser->name }}</span>
                                    <span class="d-block small text-secondary">{{ $userRole }}</span>
                                </span>
                                <i class="bi bi-chevron-down small text-secondary d-none d-lg-block"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                                <li><span class="dropdown-item-text small text-secondary">{{ $currentUser->email }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign out
                                    </a>
                                </li>
                            </ul>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </div>
                    </div>
                </header>

                <main class="nhims-content">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <div class="nhims-guest-shell">
            <div class="container py-4">
                <a href="{{ url('/') }}" class="nhims-guest-brand">
                    <span class="nhims-brand-mark" aria-hidden="true"><i class="bi bi-heart-pulse"></i></span>
                    <span>NHIMS</span>
                </a>
            </div>
            @yield('content')
        </div>
    @endauth

    @stack('scripts')
</body>
</html>
