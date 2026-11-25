@extends('layouts.app')

@section('title', ($patient->exists ? 'Edit patient' : 'Register patient').' | NHIMS')
@section('page_title', $patient->exists ? 'Edit patient profile' : 'Register patient')

@section('content')
    @php $emergency = $patient->emergencyContacts->first(); @endphp
    <div class="nhims-page-heading"><h1>{{ $patient->exists ? 'Edit patient profile' : 'Register patient' }}</h1><p>Capture clear identity, contact, and emergency information.</p></div>
    <section class="nhims-surface p-4 p-lg-5">
        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if ($patient->exists) @method('PUT') @endif
            <div class="row g-4">
                <div class="col-md-8"><label for="name" class="form-label">Full name</label><input id="name" name="name" value="{{ old('name', $patient->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4"><label for="sex" class="form-label">Sex</label><select id="sex" name="sex" class="form-select" required>@foreach(['female','male','other'] as $sex)<option value="{{ $sex }}" @selected(old('sex', $patient->sex) === $sex)>{{ ucfirst($sex) }}</option>@endforeach</select></div>
                <div class="col-md-4"><label for="date_of_birth" class="form-label">Date of birth</label><input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($patient->date_of_birth)->format('Y-m-d')) }}" class="form-control @error('date_of_birth') is-invalid @enderror" required>@error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4"><label for="phone" class="form-label">Phone</label><input id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4"><label for="email" class="form-label">Email</label><input id="email" type="email" name="email" value="{{ old('email', $patient->email) }}" class="form-control @error('email') is-invalid @enderror">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="demo_national_id" class="form-label">Demo national ID</label><input id="demo_national_id" name="demo_national_id" value="{{ old('demo_national_id', $patient->demo_national_id) }}" class="form-control @error('demo_national_id') is-invalid @enderror" placeholder="Synthetic value only">@error('demo_national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="registered_at_facility_id" class="form-label">Registered facility</label><select id="registered_at_facility_id" name="registered_at_facility_id" class="form-select @error('registered_at_facility_id') is-invalid @enderror" required>@foreach($facilities as $facility)<option value="{{ $facility->id }}" @selected((string) old('registered_at_facility_id', $patient->registered_at_facility_id) === (string) $facility->id)>{{ $facility->name }}</option>@endforeach</select>@error('registered_at_facility_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label for="address" class="form-label">Address</label><textarea id="address" name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $patient->address) }}</textarea>@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="allergies" class="form-label">Allergies</label><textarea id="allergies" name="allergies" rows="3" class="form-control">{{ old('allergies', $patient->allergies) }}</textarea></div>
                <div class="col-md-6"><label for="conditions" class="form-label">Existing conditions</label><textarea id="conditions" name="conditions" rows="3" class="form-control">{{ old('conditions', $patient->conditions) }}</textarea></div>
                <div class="col-12"><hr><h2 class="h5 mb-3">Emergency contact <span class="text-secondary fw-normal small">Optional</span></h2></div>
                <div class="col-md-6"><label for="emergency_name" class="form-label">Name</label><input id="emergency_name" name="emergency_name" value="{{ old('emergency_name', $emergency?->name) }}" class="form-control"></div>
                <div class="col-md-6"><label for="emergency_relationship" class="form-label">Relationship</label><input id="emergency_relationship" name="emergency_relationship" value="{{ old('emergency_relationship', $emergency?->relationship) }}" class="form-control"></div>
                <div class="col-md-6"><label for="emergency_phone" class="form-label">Phone</label><input id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $emergency?->phone) }}" class="form-control"></div>
                <div class="col-md-6"><label for="emergency_address" class="form-label">Address</label><input id="emergency_address" name="emergency_address" value="{{ old('emergency_address', $emergency?->address) }}" class="form-control"></div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top"><a href="{{ $patient->exists ? route('patients.show', $patient) : route('patients.index') }}" class="btn btn-light">Cancel</a><button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-2"></i>{{ $patient->exists ? 'Save changes' : 'Register patient' }}</button></div>
        </form>
    </section>
@endsection
