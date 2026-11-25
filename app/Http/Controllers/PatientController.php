<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Patient::class);
        $user = $request->user();
        $query = Patient::with('registeredAtFacility')->latest('id');

        if ($user->hasRole('patient')) {
            $query->whereKey($user->patient_id ?? 0);
        } elseif ($user->hasRole('doctor')) {
            $query->where(function ($scope) use ($user) {
                $scope->whereHas('appointments', fn ($appointments) => $appointments->where('doctor_id', $user->id))
                    ->orWhereIn('registered_at_facility_id', $user->facilities()->pluck('facilities.id'));
            });
        } elseif ($user->hasRole('receptionist')) {
            $query->whereIn('registered_at_facility_id', $user->facilities()->pluck('facilities.id'));
        }

        if ($request->filled('search')) {
            $query->search($request->string('search')->toString());
        }

        return view('patients.index', [
            'patients' => $query->paginate(12)->withQueryString(),
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Patient::class);

        return view('patients.form', [
            'patient' => new Patient(),
            'facilities' => $this->availableFacilities($request->user()),
            'formAction' => route('patients.store'),
        ]);
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->ensureFacilityAccess($request, (int) $data['registered_at_facility_id']);
        if (Patient::where('name', $data['name'])->where('phone', $data['phone'])->exists()) {
            return back()->withInput()->withErrors(['phone' => 'A patient with this name and phone number already exists. Review the existing profile before registering again.']);
        }
        $emergency = $this->extractEmergencyContact($data);
        $patient = DB::transaction(function () use ($data, $emergency, $request): Patient {
            $patient = Patient::create(array_merge($data, ['patient_number' => $this->nextPatientNumber()]));

            if ($emergency) {
                $patient->emergencyContacts()->create($emergency);
            }

            $this->audit($request, $patient, 'patient.created');

            return $patient;
        });

        return redirect()->route('patients.show', $patient)->with('status', 'Patient registered successfully.');
    }

    public function show(Request $request, Patient $patient): View
    {
        if (! $request->user()->can('view', $patient)) {
            AuditLog::create([
                'actor_id' => $request->user()->id,
                'facility_id' => $patient->registered_at_facility_id,
                'action' => 'patient.view.denied',
                'auditable_type' => Patient::class,
                'auditable_id' => $patient->id,
                'outcome' => 'denied',
                'metadata' => ['reason' => 'policy denied patient record access'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            abort(403, 'You are not authorized to view this patient record.');
        }

        $this->authorize('view', $patient);
        $patient->load([
            'registeredAtFacility',
            'emergencyContacts',
            'appointments.doctor',
            'appointments.facility',
            'consultations.doctor',
            'prescriptions.items',
            'labOrders.items.test',
            'labOrders.items.result',
        ]);
        $this->audit($request, $patient, 'patient.viewed');

        return view('patients.show', compact('patient'));
    }

    public function edit(Request $request, Patient $patient): View
    {
        $this->authorize('update', $patient);

        return view('patients.form', [
            'patient' => $patient->load('emergencyContacts'),
            'facilities' => $this->availableFacilities($request->user()),
            'formAction' => route('patients.update', $patient),
        ]);
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);
        $data = $request->validated();
        $this->ensureFacilityAccess($request, (int) $data['registered_at_facility_id']);
        $emergency = $this->extractEmergencyContact($data);
        $patient->update($data);

        if ($emergency) {
            $patient->emergencyContacts()->updateOrCreate(['patient_id' => $patient->id], $emergency);
        }

        $this->audit($request, $patient, 'patient.updated');

        return redirect()->route('patients.show', $patient)->with('status', 'Patient profile updated successfully.');
    }

    private function availableFacilities($user)
    {
        return $user->hasRole('admin')
            ? Facility::active()->orderBy('name')->get()
            : $user->facilities()->where('facilities.status', 'active')->orderBy('name')->get();
    }

    private function ensureFacilityAccess(Request $request, int $facilityId): void
    {
        if (! $request->user()->hasRole('admin')) {
            abort_unless($request->user()->facilities()->whereKey($facilityId)->exists(), 403);
        }
    }

    private function extractEmergencyContact(array &$data): ?array
    {
        $contact = [
            'name' => $data['emergency_name'] ?? null,
            'relationship' => $data['emergency_relationship'] ?? null,
            'phone' => $data['emergency_phone'] ?? null,
            'address' => $data['emergency_address'] ?? null,
        ];
        unset($data['emergency_name'], $data['emergency_relationship'], $data['emergency_phone'], $data['emergency_address']);

        return $contact['name'] && $contact['phone'] ? $contact : null;
    }

    private function nextPatientNumber(): string
    {
        $number = (int) Patient::max('id') + 1;

        do {
            $patientNumber = sprintf('NHIMS-%06d', $number++);
        } while (Patient::where('patient_number', $patientNumber)->exists());

        return $patientNumber;
    }

    private function audit(Request $request, Patient $patient, string $action): void
    {
        AuditLog::create([
            'actor_id' => $request->user()->id,
            'facility_id' => $patient->registered_at_facility_id,
            'action' => $action,
            'auditable_type' => Patient::class,
            'auditable_id' => $patient->id,
            'outcome' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
