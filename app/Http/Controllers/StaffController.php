<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(): View
    {
        return view('admin.staff.index', [
            'staff' => User::with(['role', 'facilities'])
                ->whereHas('role', fn ($query) => $query->where('key', '!=', 'patient'))
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.staff.form', [
            'staffMember' => new User(),
            'roles' => Role::where('key', '!=', 'patient')->orderBy('name')->get(),
            'facilities' => Facility::active()->orderBy('name')->get(),
            'formAction' => route('admin.staff.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $facilityIds = $data['facility_ids'];
        unset($data['facility_ids']);
        $data['password'] = Hash::make($data['password']);
        $staffMember = User::create($data);
        $this->syncFacilities($staffMember, $facilityIds);
        $this->logStaffChange($staffMember, 'staff.created', $facilityIds);

        return redirect()->route('admin.staff.index')->with('status', 'Staff account created successfully.');
    }

    public function edit(User $staffMember): View
    {
        return view('admin.staff.form', [
            'staffMember' => $staffMember->load('facilities'),
            'roles' => Role::where('key', '!=', 'patient')->orderBy('name')->get(),
            'facilities' => Facility::active()->orderBy('name')->get(),
            'formAction' => route('admin.staff.update', $staffMember),
        ]);
    }

    public function update(Request $request, User $staffMember): RedirectResponse
    {
        $data = $request->validate($this->rules($staffMember, true));
        $facilityIds = $data['facility_ids'];
        unset($data['facility_ids']);
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $staffMember->update($data);
        $this->syncFacilities($staffMember, $facilityIds);
        $this->logStaffChange($staffMember, 'staff.updated', $facilityIds);

        return redirect()->route('admin.staff.index')->with('status', 'Staff account updated successfully.');
    }

    public function deactivate(User $staffMember): RedirectResponse
    {
        abort_if($staffMember->is(auth()->user()), 422, 'You cannot deactivate your own account.');
        $staffMember->update(['status' => 'inactive']);
        $this->logStaffChange($staffMember, 'staff.deactivated', $staffMember->facilities()->pluck('facilities.id')->all());

        return redirect()->route('admin.staff.index')->with('status', 'Staff account deactivated.');
    }

    private function syncFacilities(User $staffMember, array $facilityIds): void
    {
        $pivot = collect($facilityIds)->values()->mapWithKeys(fn ($facilityId, $index) => [
            $facilityId => ['is_primary' => $index === 0, 'active_from' => today()],
        ])->all();
        $staffMember->facilities()->sync($pivot);
    }

    private function logStaffChange(User $staffMember, string $action, array $facilityIds): void
    {
        AuditLog::create([
            'actor_id' => auth()->id(),
            'facility_id' => $facilityIds[0] ?? null,
            'action' => $action,
            'auditable_type' => User::class,
            'auditable_id' => $staffMember->id,
            'outcome' => 'success',
            'metadata' => ['facility_ids' => array_values($facilityIds), 'role_id' => $staffMember->role_id],
        ]);
    }

    private function rules(?User $staffMember = null, bool $editing = false): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($staffMember?->id)],
            'role_id' => ['required', Rule::exists('roles', 'id')->where(fn ($query) => $query->where('key', '!=', 'patient'))],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => [$editing ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'facility_ids' => ['required', 'array', 'min:1'],
            'facility_ids.*' => ['integer', 'exists:facilities,id'],
        ];
    }
}
