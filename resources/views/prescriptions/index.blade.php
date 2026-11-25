@extends('layouts.app')
@section('title', 'Prescriptions | NHIMS')
@section('page_title', 'Prescriptions')
@section('content')
    <div class="nhims-page-heading"><h1>Prescriptions</h1><p>Review issued prescriptions according to your role and facility scope.</p></div>
    @if(session('status'))<div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}</div>@endif
    <section class="nhims-surface"><div class="table-responsive"><table class="table nhims-table align-middle mb-0"><thead><tr><th class="ps-4">Patient</th><th>Doctor</th><th>Facility</th><th>Issued</th><th>Status</th><th class="text-end pe-4">Action</th></tr></thead><tbody>@forelse($prescriptions as $prescription)<tr><td class="ps-4">{{ $prescription->patient?->name }}</td><td>{{ $prescription->doctor?->name }}</td><td>{{ $prescription->facility?->district }}</td><td>{{ optional($prescription->issued_at)->format('d M Y') }}</td><td>{{ ucfirst(str_replace('_',' ',$prescription->status)) }}</td><td class="text-end pe-4"><a href="{{ route('prescriptions.show', $prescription) }}" class="btn btn-sm btn-light text-primary">View</a></td></tr>@empty<tr><td colspan="6" class="p-5 text-center text-secondary">No prescriptions available.</td></tr>@endforelse</tbody></table></div>@if($prescriptions->hasPages())<div class="p-3 border-top">{{ $prescriptions->links() }}</div>@endif</section>
@endsection
