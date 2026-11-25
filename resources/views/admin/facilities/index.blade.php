@extends('layouts.app')

@section('title', 'Facilities | NHIMS')
@section('page_title', 'Facilities')

@section('content')
    <div class="nhims-page-heading d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1>Facilities</h1>
            <p>Manage the healthcare locations connected to NHIMS.</p>
        </div>
        <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary"><i class="bi bi-building-add me-2"></i>Add facility</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}</div>
    @endif

    <section class="nhims-surface">
        <div class="table-responsive">
            <table class="table nhims-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Facility</th>
                        <th>Type</th>
                        <th>District</th>
                        <th>Patients</th>
                        <th>Appointments</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($facilities as $facility)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ $facility->name }}</div>
                                <div class="small text-secondary">{{ $facility->code }}</div>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $facility->type)) }}</td>
                            <td>{{ $facility->district }}</td>
                            <td>{{ number_format($facility->patients_count) }}</td>
                            <td>{{ number_format($facility->appointments_count) }}</td>
                            <td><span class="nhims-status {{ $facility->status === 'active' ? 'nhims-status-success' : 'nhims-status-danger' }}">{{ ucfirst($facility->status) }}</span></td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.facilities.edit', $facility) }}" class="btn btn-sm btn-light" aria-label="Edit {{ $facility->name }}"><i class="bi bi-pencil"></i></a>
                                    @if ($facility->status === 'active')
                                        <form method="POST" action="{{ route('admin.facilities.deactivate', $facility) }}" onsubmit="return confirm('Deactivate this facility?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Deactivate {{ $facility->name }}"><i class="bi bi-slash-circle"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-5 text-center text-secondary">No facilities found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($facilities->hasPages())
            <div class="p-3 border-top">{{ $facilities->links() }}</div>
        @endif
    </section>
@endsection
