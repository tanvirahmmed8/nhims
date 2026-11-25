@extends('layouts.app')

@section('title', $patient->name.' | NHIMS')
@section('page_title', 'Patient profile')

@section('content')
    @php $canEdit = auth()->user()->can('update', $patient); @endphp

    <div class="nhims-page-heading d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <div class="small text-secondary mb-2"><a href="{{ route('patients.index') }}" class="text-decoration-none">Patients</a> / Profile</div>
            <h1>{{ $patient->name }}</h1>
            <p>{{ $patient->patient_number }} · {{ $patient->registeredAtFacility?->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary"><i class="bi bi-calendar-plus me-2"></i>Book appointment</a>
            @if ($canEdit)
                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-2"></i>Edit profile</a>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <section class="nhims-surface p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="nhims-avatar fs-5">{{ strtoupper(substr($patient->name, 0, 1)) }}</span>
                    <div><h2 class="h5 mb-1">{{ $patient->name }}</h2><span class="nhims-status nhims-status-success">{{ ucfirst($patient->status) }}</span></div>
                </div>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary">Date of birth</dt><dd class="col-7">{{ $patient->date_of_birth->format('d M Y') }}</dd>
                    <dt class="col-5 text-secondary">Sex</dt><dd class="col-7">{{ ucfirst($patient->sex) }}</dd>
                    <dt class="col-5 text-secondary">Phone</dt><dd class="col-7">{{ $patient->phone }}</dd>
                    <dt class="col-5 text-secondary">Email</dt><dd class="col-7 text-break">{{ $patient->email ?: 'Not provided' }}</dd>
                    <dt class="col-5 text-secondary">Address</dt><dd class="col-7">{{ $patient->address }}</dd>
                </dl>
                <hr>
                <h3 class="h6">Emergency contact</h3>
                @if ($patient->emergencyContacts->first())
                    <p class="small mb-0">{{ $patient->emergencyContacts->first()->name }} · {{ $patient->emergencyContacts->first()->relationship }}<br>{{ $patient->emergencyContacts->first()->phone }}</p>
                @else
                    <p class="small text-secondary mb-0">No emergency contact recorded.</p>
                @endif
                <hr>
                <h3 class="h6">Clinical flags</h3>
                <p class="small mb-1"><strong>Allergies</strong><br>{{ $patient->allergies ?: 'None recorded' }}</p>
                <p class="small mb-0"><strong>Conditions</strong><br>{{ $patient->conditions ?: 'None recorded' }}</p>
            </section>
        </div>

        <div class="col-12 col-xl-8">
            <div class="row g-3 mb-4">
                <div class="col-sm-4"><div class="nhims-metric-card"><p class="nhims-metric-label mt-0">Appointments</p><p class="nhims-metric-value">{{ $patient->appointments->count() }}</p></div></div>
                <div class="col-sm-4"><div class="nhims-metric-card"><p class="nhims-metric-label mt-0">Consultations</p><p class="nhims-metric-value">{{ $patient->consultations->count() }}</p></div></div>
                <div class="col-sm-4"><div class="nhims-metric-card"><p class="nhims-metric-label mt-0">Lab orders</p><p class="nhims-metric-value">{{ $patient->labOrders->count() }}</p></div></div>
            </div>

            <section class="nhims-surface p-4 mb-4">
                <h2 class="nhims-section-title">Appointments</h2>
                <div class="table-responsive mt-3"><table class="table nhims-table mb-0"><thead><tr><th>Date</th><th>Doctor</th><th>Reason</th><th>Status</th></tr></thead><tbody>
                    @forelse ($patient->appointments as $appointment)
                        <tr><td>{{ $appointment->scheduled_at->format('d M Y, h:i A') }}</td><td>{{ $appointment->doctor?->name }}</td><td>{{ $appointment->reason }}</td><td>{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="text-secondary">No appointments recorded.</td></tr>
                    @endforelse
                </tbody></table></div>
            </section>

            <section class="nhims-surface p-4 mb-4">
                <h2 class="nhims-section-title">Clinical history</h2>
                <div class="mt-3">
                    @forelse ($patient->consultations as $consultation)
                        <div class="nhims-activity-item"><div class="nhims-activity-icon"><i class="bi bi-clipboard2-pulse"></i></div><div><p class="nhims-activity-title">{{ $consultation->diagnosis ?: 'Consultation record' }}</p><p class="nhims-activity-meta">{{ $consultation->doctor?->name }} · {{ ucfirst($consultation->status) }}</p></div></div>
                    @empty
                        <p class="text-secondary mb-0">No consultation history recorded.</p>
                    @endforelse
                </div>
            </section>

            <div class="row g-4">
                <div class="col-12 col-lg-6"><section class="nhims-surface p-4 h-100"><h2 class="nhims-section-title">Prescriptions</h2><div class="mt-3">@forelse ($patient->prescriptions as $prescription)<div class="nhims-activity-item"><div class="nhims-activity-icon"><i class="bi bi-file-medical"></i></div><div><p class="nhims-activity-title">Prescription #{{ $prescription->id }}</p><p class="nhims-activity-meta">{{ ucfirst(str_replace('_', ' ', $prescription->status)) }} · {{ $prescription->items->count() }} item(s)</p></div></div>@empty<p class="text-secondary mb-0">No prescriptions recorded.</p>@endforelse</div></section></div>
                <div class="col-12 col-lg-6"><section class="nhims-surface p-4 h-100"><h2 class="nhims-section-title">Laboratory history</h2><div class="mt-3">@forelse ($patient->labOrders as $order)<div class="nhims-activity-item"><div class="nhims-activity-icon"><i class="bi bi-eyedropper"></i></div><div><p class="nhims-activity-title">Lab order #{{ $order->id }}</p><p class="nhims-activity-meta">{{ ucfirst($order->status) }} · {{ $order->items->count() }} test(s)</p>@foreach($order->items as $item)@if($item->result && $item->result->status === 'published')<p class="small text-dark mb-0">{{ $item->test?->name }}: <strong>{{ $item->result->value }} {{ $item->result->unit }}</strong></p>@endif @endforeach</div></div>@empty<p class="text-secondary mb-0">No laboratory orders recorded.</p>@endforelse</div></section></div>
            </div>
        </div>
    </div>
@endsection
