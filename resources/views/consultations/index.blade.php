@extends('layouts.app')
@section('title', 'Consultations | NHIMS')
@section('page_title', 'Consultations')
@section('content')
    <div class="nhims-page-heading"><h1>Consultation queue</h1><p>Review today’s appointments and record the clinical encounter.</p></div>
    <section class="nhims-surface"><div class="table-responsive"><table class="table nhims-table align-middle mb-0"><thead><tr><th class="ps-4">Patient</th><th>Facility</th><th>Scheduled</th><th>Status</th><th class="text-end pe-4">Action</th></tr></thead><tbody>
        @forelse($appointments as $appointment)
            <tr><td class="ps-4"><div class="fw-semibold text-dark">{{ $appointment->patient?->name }}</div><div class="small text-secondary">{{ $appointment->patient?->patient_number }}</div></td><td>{{ $appointment->facility?->district }}</td><td>{{ $appointment->scheduled_at->format('d M Y, h:i A') }}</td><td>{{ ucfirst(str_replace('_',' ',$appointment->status)) }}</td><td class="text-end pe-4">@if($appointment->consultation)<a href="{{ route('consultations.show', $appointment->consultation) }}" class="btn btn-sm btn-light text-primary">Open consultation</a>@else<a href="{{ route('consultations.create', $appointment) }}" class="btn btn-sm btn-primary">Start consultation</a>@endif</td></tr>
        @empty
            <tr><td colspan="5" class="p-5 text-center text-secondary">No appointments are assigned to this queue.</td></tr>
        @endforelse
    </tbody></table></div>@if($appointments->hasPages())<div class="p-3 border-top">{{ $appointments->links() }}</div>@endif</section>
@endsection
