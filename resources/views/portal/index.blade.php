@extends('layouts.app')

@section('title', 'My health portal | NHIMS')
@section('page_title', 'My health portal')

@section('content')
    <div class="nhims-page-heading">
        <h1>Welcome, {{ $patient->name }}</h1>
        <p>Your appointments, prescriptions, and published laboratory results in one place.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="nhims-metric-card">
                <p class="nhims-metric-label mt-0">Upcoming appointments</p>
                <p class="nhims-metric-value">{{ $appointments->where('scheduled_at', '>=', now())->count() }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="nhims-metric-card">
                <p class="nhims-metric-label mt-0">Active prescriptions</p>
                <p class="nhims-metric-value">{{ $prescriptions->whereIn('status', ['issued', 'partially_dispensed'])->count() }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="nhims-metric-card">
                <p class="nhims-metric-label mt-0">Published lab results</p>
                <p class="nhims-metric-value">{{ $labResults->count() }}</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <section class="nhims-surface p-4 h-100">
                <h2 class="nhims-section-title">My profile</h2>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <p class="small text-secondary mb-1">Patient number</p>
                        <p class="fw-semibold">{{ $patient->patient_number }}</p>
                        <p class="small text-secondary mb-1">Date of birth</p>
                        <p class="fw-semibold">{{ $patient->date_of_birth->format('d M Y') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="small text-secondary mb-1">Phone</p>
                        <p class="fw-semibold">{{ $patient->phone }}</p>
                        <p class="small text-secondary mb-1">Emergency contact</p>
                        <p class="fw-semibold">{{ $patient->emergencyContacts->first()?->name ?: 'Not recorded' }}</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-5">
            <section class="nhims-surface p-4 h-100">
                <h2 class="nhims-section-title">Upcoming appointments</h2>
                <div class="mt-3">
                    @forelse($appointments->where('scheduled_at', '>=', now())->take(3) as $appointment)
                        <div class="nhims-activity-item">
                            <div class="nhims-activity-icon"><i class="bi bi-calendar2-check"></i></div>
                            <div>
                                <p class="nhims-activity-title">{{ $appointment->scheduled_at->format('d M, h:i A') }}</p>
                                <p class="nhims-activity-meta">{{ $appointment->doctor?->name }} &middot; {{ ucfirst($appointment->status) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-secondary mb-0">No upcoming appointments.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-6">
            <section class="nhims-surface p-4 h-100">
                <h2 class="nhims-section-title">Prescriptions</h2>
                <div class="mt-3">
                    @forelse($prescriptions->take(4) as $prescription)
                        <div class="nhims-activity-item">
                            <div class="nhims-activity-icon"><i class="bi bi-file-medical"></i></div>
                            <div>
                                <p class="nhims-activity-title">Prescription #{{ $prescription->id }}</p>
                                <p class="nhims-activity-meta">{{ $prescription->items->pluck('medicine_name')->join(', ') }} &middot; {{ ucfirst(str_replace('_', ' ', $prescription->status)) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-secondary mb-0">No prescriptions available.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-6">
            <section class="nhims-surface p-4 h-100">
                <h2 class="nhims-section-title">Published laboratory results</h2>
                <div class="mt-3">
                    @forelse($labResults->take(4) as $result)
                        <div class="nhims-activity-item">
                            <div class="nhims-activity-icon"><i class="bi bi-eyedropper"></i></div>
                            <div>
                                <p class="nhims-activity-title">{{ $result->orderItem?->test?->name }}</p>
                                <p class="nhims-activity-meta">{{ $result->value }} {{ $result->unit }} &middot; {{ optional($result->published_at)->format('d M Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-secondary mb-0">No published laboratory results.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
