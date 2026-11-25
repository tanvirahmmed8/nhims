<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class FacilityController extends Controller
{
    public function index(): View
    {
        return view('admin.facilities.index', [
            'facilities' => Facility::withCount(['users', 'patients', 'appointments'])->orderBy('name')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.facilities.form', ['facility' => new Facility(), 'formAction' => route('admin.facilities.store')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $facility = Facility::create($data);
        AuditLog::create([
            'actor_id' => auth()->id(),
            'facility_id' => $facility->id,
            'action' => 'facility.created',
            'auditable_type' => Facility::class,
            'auditable_id' => $facility->id,
            'outcome' => 'success',
        ]);

        return redirect()->route('admin.facilities.index')->with('status', 'Facility created successfully.');
    }

    public function edit(Facility $facility): View
    {
        return view('admin.facilities.form', [
            'facility' => $facility,
            'formAction' => route('admin.facilities.update', $facility),
        ]);
    }

    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $data = $request->validate($this->rules($facility));
        $facility->update($data);
        AuditLog::create([
            'actor_id' => auth()->id(),
            'facility_id' => $facility->id,
            'action' => 'facility.updated',
            'auditable_type' => Facility::class,
            'auditable_id' => $facility->id,
            'outcome' => 'success',
            'metadata' => ['fields' => array_keys($data)],
        ]);

        return redirect()->route('admin.facilities.index')->with('status', 'Facility updated successfully.');
    }

    public function deactivate(Facility $facility): RedirectResponse
    {
        $facility->update(['status' => 'inactive']);
        AuditLog::create([
            'actor_id' => auth()->id(),
            'facility_id' => $facility->id,
            'action' => 'facility.deactivated',
            'auditable_type' => Facility::class,
            'auditable_id' => $facility->id,
            'outcome' => 'success',
        ]);

        return redirect()->route('admin.facilities.index')->with('status', 'Facility deactivated.');
    }

    private function rules(?Facility $facility = null): array
    {
        return [
            'code' => ['required', 'string', 'max:30', Rule::unique('facilities', 'code')->ignore($facility?->id)],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:50'],
            'district' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
