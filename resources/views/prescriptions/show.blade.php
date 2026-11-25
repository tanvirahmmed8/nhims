@extends('layouts.app')
@section('title', 'Prescription details | NHIMS')
@section('page_title', 'Prescription details')
@section('content')
    <div class="nhims-page-heading d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3"><div><h1>Prescription #{{ $prescription->id }}</h1><p>{{ $prescription->patient?->name }} · {{ $prescription->doctor?->name }}</p></div><span class="nhims-status nhims-status-{{ $prescription->status === 'dispensed' ? 'success' : 'info' }}">{{ ucfirst(str_replace('_',' ',$prescription->status)) }}</span></div>
    @if(session('status'))<div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}</div>@endif
    <section class="nhims-surface p-4"><div class="table-responsive"><table class="table nhims-table align-middle mb-0"><thead><tr><th>Medicine</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Duration</th><th>Quantity</th><th>Instructions</th></tr></thead><tbody>@foreach($prescription->items as $item)<tr><td class="fw-semibold">{{ $item->medicine_name }}</td><td>{{ $item->dose }}</td><td>{{ $item->route }}</td><td>{{ $item->frequency }}</td><td>{{ $item->duration }}</td><td>{{ $item->quantity }}</td><td>{{ $item->instructions ?: '—' }}</td></tr>@endforeach</tbody></table></div></section>
@endsection
