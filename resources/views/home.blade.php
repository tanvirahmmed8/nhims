@extends('layouts.app')

@section('title', 'Overview | NHIMS')
@section('page_title', 'Good morning, '.(auth()->user()->name ?? 'team member'))

@section('content')
    <div class="nhims-page-heading d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1>Care operations overview</h1>
            <p>Monitor today’s activity and keep every patient interaction connected.</p>
        </div>
        @if (auth()->user()->hasAnyRole(['admin', 'receptionist']))
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('patients.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Register patient</a>
                <a href="{{ route('appointments.create') }}" class="btn btn-outline-secondary"><i class="bi bi-calendar-plus me-2"></i>Book appointment</a>
            </div>
        @endif
    </div>

    @if (session('status'))
        <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="nhims-metric-card">
                <div class="nhims-metric-icon text-primary bg-primary-subtle"><i class="bi bi-people"></i></div>
                <p class="nhims-metric-label">{{ $isPatient ? 'My patient profile' : 'Total patients' }}</p>
                <p class="nhims-metric-value">{{ number_format($metrics['patients']) }}</p>
                <p class="nhims-metric-note"><i class="bi bi-arrow-up-right me-1"></i>Connected care records</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="nhims-metric-card">
                <div class="nhims-metric-icon text-info bg-info-subtle"><i class="bi bi-calendar2-check"></i></div>
                <p class="nhims-metric-label">Appointments today</p>
                <p class="nhims-metric-value">{{ number_format($metrics['appointmentsToday']) }}</p>
                <p class="nhims-metric-note"><i class="bi bi-clock me-1"></i>Across active queues</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="nhims-metric-card">
                <div class="nhims-metric-icon text-warning bg-warning-subtle"><i class="bi bi-eyedropper"></i></div>
                <p class="nhims-metric-label">Pending laboratory</p>
                <p class="nhims-metric-value">{{ number_format($metrics['pendingLabs']) }}</p>
                <p class="nhims-metric-note"><i class="bi bi-arrow-right me-1"></i>Orders needing attention</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="nhims-metric-card">
                <div class="nhims-metric-icon text-success bg-success-subtle"><i class="bi bi-file-medical"></i></div>
                <p class="nhims-metric-label">Active prescriptions</p>
                <p class="nhims-metric-value">{{ number_format($metrics['activePrescriptions']) }}</p>
                <p class="nhims-metric-note"><i class="bi bi-capsule me-1"></i>Issued or partially dispensed</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <section class="nhims-surface h-100" aria-labelledby="appointments-heading">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 p-4 border-bottom">
                    <div>
                        <h2 id="appointments-heading" class="nhims-section-title">Upcoming appointments</h2>
                        <p class="nhims-section-subtitle">The next scheduled patient interactions.</p>
                    </div>
                    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-light text-primary fw-semibold">View schedule <i class="bi bi-arrow-up-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table nhims-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Patient</th>
                                <th>Doctor</th>
                                <th>When</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upcomingAppointments as $appointment)
                                @php
                                    $statusClass = match ($appointment->status) {
                                        'confirmed' => 'nhims-status-success',
                                        'booked' => 'nhims-status-info',
                                        'cancelled' => 'nhims-status-danger',
                                        default => 'nhims-status-warning',
                                    };
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark">{{ $appointment->patient?->name ?? 'Patient record' }}</div>
                                        <div class="small text-secondary">{{ $appointment->type }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $appointment->doctor?->name ?? 'Assigned doctor' }}</div>
                                        <div class="small text-secondary">{{ $appointment->facility?->district ?? 'Facility' }}</div>
                                    </td>
                                    <td class="text-nowrap">{{ $appointment->scheduled_at->format('d M, h:i A') }}</td>
                                    <td><span class="nhims-status {{ $statusClass }}">{{ str_replace('_', ' ', ucfirst($appointment->status)) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-5 text-center text-secondary">
                                        <i class="bi bi-calendar2-x d-block fs-2 mb-2"></i>
                                        No upcoming appointments in this view.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-4">
            <section class="nhims-surface h-100" aria-labelledby="activity-heading">
                <div class="p-4 border-bottom">
                    <h2 id="activity-heading" class="nhims-section-title">Recent activity</h2>
                    <p class="nhims-section-subtitle">A traceable view of sensitive actions.</p>
                </div>
                <div class="p-4">
                    @forelse ($recentAuditLogs as $log)
                        <div class="nhims-activity-item">
                            <div class="nhims-activity-icon"><i class="bi bi-shield-check"></i></div>
                            <div>
                                <p class="nhims-activity-title">{{ str_replace('.', ' ', ucfirst($log->action)) }}</p>
                                <p class="nhims-activity-meta">{{ $log->actor?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-secondary py-4">
                            <i class="bi bi-person-lock d-block fs-2 mb-2"></i>
                            Personal activity will appear here.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        @if (! $isPatient)
            <div class="col-12">
                <section class="nhims-surface" aria-labelledby="facility-heading">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 p-4 border-bottom">
                        <div>
                            <h2 id="facility-heading" class="nhims-section-title">Facility snapshot</h2>
                            <p class="nhims-section-subtitle">A simple view of activity across connected facilities.</p>
                        </div>
                        <a href="#facilities" class="btn btn-sm btn-outline-secondary">Manage facilities</a>
                    </div>
                    <div class="row g-0">
                        @forelse ($facilitySummary as $facility)
                            <div class="col-12 col-md-6 col-xl-4 border-end border-bottom p-4">
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div>
                                        <p class="fw-semibold text-dark mb-1">{{ $facility->name }}</p>
                                        <p class="small text-secondary mb-0">{{ $facility->district }} · {{ ucfirst(str_replace('_', ' ', $facility->type)) }}</p>
                                    </div>
                                    <span class="nhims-status nhims-status-success">Active</span>
                                </div>
                                <div class="d-flex gap-4 mt-3">
                                    <div><span class="d-block small text-secondary">Patients</span><strong>{{ number_format($facility->patients_count) }}</strong></div>
                                    <div><span class="d-block small text-secondary">Appointments</span><strong>{{ number_format($facility->appointments_count) }}</strong></div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-secondary">No facility data available.</div>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif
    </div>
@endsection
