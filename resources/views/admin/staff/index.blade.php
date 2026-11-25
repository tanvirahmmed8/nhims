@extends('layouts.app')

@section('title', 'Staff | NHIMS')
@section('page_title', 'Staff management')

@section('content')
    <div class="nhims-page-heading d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <h1>Staff management</h1>
            <p>Assign roles and facility access to NHIMS team members.</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Add staff</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}</div>
    @endif

    <section class="nhims-surface">
        <div class="table-responsive">
            <table class="table nhims-table align-middle mb-0">
                <thead>
                    <tr><th class="ps-4">Staff member</th><th>Role</th><th>Facilities</th><th>Status</th><th class="text-end pe-4">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($staff as $staffMember)
                        <tr>
                            <td class="ps-4"><div class="fw-semibold text-dark">{{ $staffMember->name }}</div><div class="small text-secondary">{{ $staffMember->email }}</div></td>
                            <td>{{ $staffMember->role?->name ?? 'Unassigned' }}</td>
                            <td><span class="small">{{ $staffMember->facilities->pluck('name')->join(', ') ?: 'No facility assigned' }}</span></td>
                            <td><span class="nhims-status {{ $staffMember->status === 'active' ? 'nhims-status-success' : 'nhims-status-danger' }}">{{ ucfirst($staffMember->status) }}</span></td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.staff.edit', $staffMember) }}" class="btn btn-sm btn-light" aria-label="Edit {{ $staffMember->name }}"><i class="bi bi-pencil"></i></a>
                                    @if ($staffMember->status === 'active' && $staffMember->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.staff.deactivate', $staffMember) }}" onsubmit="return confirm('Deactivate this staff account?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Deactivate {{ $staffMember->name }}"><i class="bi bi-person-dash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-5 text-center text-secondary">No staff accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($staff->hasPages())<div class="p-3 border-top">{{ $staff->links() }}</div>@endif
    </section>
@endsection
