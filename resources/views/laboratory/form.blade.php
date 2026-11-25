@extends('layouts.app')
@section('title', 'Order laboratory tests | NHIMS')
@section('page_title', 'Order laboratory tests')
@section('content')
    <div class="nhims-page-heading"><h1>Order laboratory tests</h1><p>{{ $consultation->patient?->name }} · {{ $consultation->diagnosis }}</p></div>
    <section class="nhims-surface p-4"><form method="POST" action="{{ route('laboratory.store', $consultation) }}">@csrf
        <div class="row g-3"><div class="col-md-4"><label for="priority" class="form-label">Priority</label><select id="priority" name="priority" class="form-select"><option value="routine">Routine</option><option value="urgent">Urgent</option></select></div><div class="col-md-4"><label for="sample_type" class="form-label">Sample type</label><input id="sample_type" name="sample_type" class="form-control" placeholder="Blood, urine, swab"></div><div class="col-12"><label class="form-label">Requested tests</label><div class="row g-2">@foreach($tests as $test)<div class="col-md-6"><label class="form-check border rounded-3 p-3 ps-5"><input class="form-check-input" type="checkbox" name="test_ids[]" value="{{ $test->id }}"><span class="fw-semibold d-block">{{ $test->name }}</span><span class="small text-secondary">{{ $test->specimen_type }} · {{ $test->reference_range }}</span></label></div>@endforeach</div></div><div class="col-12"><label for="notes" class="form-label">Notes</label><textarea id="notes" name="notes" rows="3" class="form-control"></textarea></div></div>
        <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top"><a href="{{ route('consultations.show', $consultation) }}" class="btn btn-light">Cancel</a><button class="btn btn-primary" type="submit"><i class="bi bi-eyedropper me-2"></i>Create lab order</button></div>
    </form></section>
@endsection
