@extends('layouts.app')

@section('title', ($facility->exists ? 'Edit facility' : 'Add facility').' | NHIMS')
@section('page_title', $facility->exists ? 'Edit facility' : 'Add facility')

@section('content')
    <div class="nhims-page-heading">
        <h1>{{ $facility->exists ? 'Edit facility' : 'Add facility' }}</h1>
        <p>Keep facility identity and service context accurate for staff workflows.</p>
    </div>

    <section class="nhims-surface p-4 p-lg-5">
        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if ($facility->exists) @method('PUT') @endif
            <div class="row g-4">
                <div class="col-md-4">
                    <label for="code" class="form-label">Facility code</label>
                    <input id="code" name="code" value="{{ old('code', $facility->code) }}" class="form-control @error('code') is-invalid @enderror" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label for="name" class="form-label">Facility name</label>
                    <input id="name" name="name" value="{{ old('name', $facility->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Facility type</label>
                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                        @foreach (['hospital' => 'Hospital', 'clinic' => 'Clinic', 'diagnostic_centre' => 'Diagnostic centre', 'pharmacy' => 'Pharmacy'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $facility->type) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="district" class="form-label">District</label>
                    <input id="district" name="district" value="{{ old('district', $facility->district) }}" class="form-control @error('district') is-invalid @enderror" required>
                    @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="phone" class="form-label">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone', $facility->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label for="address" class="form-label">Address</label>
                    <input id="address" name="address" value="{{ old('address', $facility->address) }}" class="form-control @error('address') is-invalid @enderror" required>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" @selected(old('status', $facility->status ?: 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $facility->status) === 'inactive')>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.facilities.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-2"></i>Save facility</button>
            </div>
        </form>
    </section>
@endsection
