@extends('layouts.app')
@section('title', 'Laboratory order | NHIMS')
@section('page_title', 'Laboratory order')
@section('content')
    <div class="nhims-page-heading"><h1>Laboratory order #{{ $order->id }}</h1><p>{{ $order->patient?->name }} · {{ ucfirst($order->status) }}</p></div>
    @if(session('status'))<div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}</div>@endif
    <section class="nhims-surface p-4"><div class="table-responsive"><table class="table nhims-table align-middle mb-0"><thead><tr><th>Test</th><th>Specimen</th><th>Status</th><th>Result</th><th class="text-end">Action</th></tr></thead><tbody>@foreach($order->items as $item)<tr><td class="fw-semibold">{{ $item->test?->name }}</td><td>{{ $item->test?->specimen_type }}</td><td>{{ ucfirst($item->status) }}</td><td>@if($item->result && $item->result->status === 'published'){{ $item->result->value }} {{ $item->result->unit }}@else<span class="text-secondary">Not published</span>@endif</td><td class="text-end">@if(auth()->user()->hasRole('lab_staff') && (!$item->result || $item->result->status !== 'published'))<a href="{{ route('laboratory.result.create', $item) }}" class="btn btn-sm btn-primary">Enter result</a>@endif</td></tr>@endforeach</tbody></table></div></section>
@endsection
