@extends('layouts.app')
@section('title', 'Laboratory | NHIMS')
@section('page_title', 'Laboratory')
@section('content')
    <div class="nhims-page-heading"><h1>Laboratory orders</h1><p>Track requested tests, processing status, and published results.</p></div>
    <section class="nhims-surface"><div class="table-responsive"><table class="table nhims-table align-middle mb-0"><thead><tr><th class="ps-4">Patient</th><th>Doctor</th><th>Facility</th><th>Requested</th><th>Status</th><th class="text-end pe-4">Action</th></tr></thead><tbody>@forelse($orders as $order)<tr><td class="ps-4">{{ $order->patient?->name }}</td><td>{{ $order->doctor?->name }}</td><td>{{ $order->facility?->district }}</td><td>{{ optional($order->requested_at)->format('d M Y') }}</td><td>{{ ucfirst($order->status) }}</td><td class="text-end pe-4"><a href="{{ route('laboratory.show', $order) }}" class="btn btn-sm btn-light text-primary">View order</a></td></tr>@empty<tr><td colspan="6" class="p-5 text-center text-secondary">No laboratory orders available.</td></tr>@endforelse</tbody></table></div>@if($orders->hasPages())<div class="p-3 border-top">{{ $orders->links() }}</div>@endif</section>
@endsection
