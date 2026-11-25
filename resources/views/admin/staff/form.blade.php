@extends('layouts.app')

@section('title', ($staffMember->exists ? 'Edit staff' : 'Add staff').' | NHIMS')
@section('page_title', $staffMember->exists ? 'Edit staff member' : 'Add staff member')

@section('content')
    <div class="nhims-page-heading">
        <h1>{{ $staffMember->exists ? 'Edit staff member' : 'Add staff member' }}</h1>
        <p>Set a role and at least one active facility assignment.</p>
    </div>

    <section class="nhims-surface p-4 p-lg-5">
        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if ($staffMember->exists) @method('PUT') @endif
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="name" class="form-label">Full name</label>
                    <input id="name" name="name" value="{{ old('name', $staffMember->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $staffMember->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role</label>
                    <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">Choose role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('role_id', $staffMember->role_id) === (string) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Account status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" @selected(old('status', $staffMember->status ?: 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $staffMember->status) === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">{{ $staffMember->exists ? 'New password (optional)' : 'Password' }}</label>
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $staffMember->exists ? '' : 'required' }} autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" {{ $staffMember->exists ? '' : 'required' }} autocomplete="new-password">
                </div>
                <div class="col-12">
                    <label class="form-label">Facility access</label>
                    <div class="row g-2">
                        @foreach ($facilities as $facility)
                            <div class="col-md-6">
                                <div class="form-check border rounded-3 p-3 ps-5">
                                    <input class="form-check-input" type="checkbox" name="facility_ids[]" value="{{ $facility->id }}" id="facility-{{ $facility->id }}" @checked(in_array($facility->id, old('facility_ids', $staffMember->facilities->pluck('id')->all()), true))>
                                    <label class="form-check-label" for="facility-{{ $facility->id }}"><span class="fw-semibold d-block">{{ $facility->name }}</span><span class="small text-secondary">{{ $facility->district }} · {{ ucfirst(str_replace('_', ' ', $facility->type)) }}</span></label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('facility_ids')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                    @error('facility_ids.*')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-2"></i>Save staff member</button>
            </div>
        </form>
    </section>
@endsection
