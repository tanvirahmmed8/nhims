@extends('layouts.app')

@section('title', 'Patients | NHIMS')
@section('page_title', 'Patients')

@section('content')
    <div class="nhims-page-heading d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div><h1>Patients</h1><p>Find a patient profile and continue the care journey.</p></div>
        @can('create', App\Models\Patient::class)
            <a href="{{ route('patients.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Register patient</a>
        @endcan
    </div>

    @if (session('status'))<div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}</div>@endif

    <section class="nhims-surface">
        <form method="GET" class="p-3 border-bottom">
            <div class="row g-2 align-items-end">
                <div class="col-md-8"><label for="search" class="form-label small text-secondary">Search patient</label><input id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Name, patient number, phone, demo ID"></div>
                <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-search me-2"></i>Search</button><a class="btn btn-light" href="{{ route('patients.index') }}">Reset</a></div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table nhims-table align-middle mb-0">
                <thead><tr><th class="ps-4">Patient</th><th>Patient number</th><th>Date of birth</th><th>Facility</th><th>Phone</th><th class="text-end pe-4">Action</th></tr></thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr>
                            <td class="ps-4"><div class="fw-semibold text-dark">{{ $patient->name }}</div><div class="small text-secondary">{{ ucfirst($patient->sex) }}</div></td>
                            <td>{{ $patient->patient_number }}</td>
                            <td>{{ $patient->date_of_birth->format('d M Y') }}</td>
                            <td>{{ $patient->registeredAtFacility?->name }}</td>
                            <td>{{ $patient->phone }}</td>
                            <td class="text-end pe-4"><a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-light text-primary">View profile <i class="bi bi-arrow-up-right ms-1"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-5 text-center text-secondary"><i class="bi bi-person-x d-block fs-2 mb-2"></i>No patient records match this search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($patients->hasPages())<div class="p-3 border-top">{{ $patients->links() }}</div>@endif
    </section>
@endsection
