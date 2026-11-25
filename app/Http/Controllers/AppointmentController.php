<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);
        $user = $request->user();
        $query = Appointment::with(['patient', 'doctor', 'facility'])->orderBy('scheduled_at');

        if ($user->hasRole('patient')) {
            $query->where('patient_id', $user->patient_id ?? 0);
        } elseif ($user->hasRole('doctor')) {
            $query->where('doctor_id', $user->id);
        } elseif ($user->hasRole('receptionist')) {
            $query->whereIn('facility_id', $user->facilities()->pluck('facilities.id'));
        }

        if ($request->filled('status')) {
            $query->status($request->string('status')->toString());
        }
        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date('date'));
        }
        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->integer('facility_id'));
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->integer('doctor_id'));
        }
        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->whereHas('patient', fn ($patients) => $patients->search($term));
        }

        return view('appointments.index', [
            'appointments' => $query->paginate(12)->withQueryString(),
            'filters' => $request->only(['status', 'date', 'search', 'facility_id', 'doctor_id']),
            'filterFacilities' => $this->availableFacilities($user),
            'filterDoctors' => $this->availableDoctors($user),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Appointment::class);

        return view('appointments.form', [
            'appointment' => new Appointment(['status' => 'booked']),
            'patients' => $this->availablePatients($request->user()),
            'facilities' => $this->availableFacilities($request->user()),
            'doctors' => $this->availableDoctors($request->user()),
            'formAction' => route('appointments.store'),
        ]);
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->validateAssignment($request, $data);
        $this->ensureSlotAvailable($data);
        $appointment = Appointment::create($data);
        $this->audit($request, $appointment, 'appointment.created');

        return redirect()->route('appointments.show', $appointment)->with('status', 'Appointment booked successfully.');
    }

    public function show(Request $request, Appointment $appointment): View
    {
        $this->authorize('view', $appointment);
        $appointment->load(['patient', 'doctor', 'facility', 'consultation']);
        $this->audit($request, $appointment, 'appointment.viewed');

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Request $request, Appointment $appointment): View
    {
        $this->authorize('update', $appointment);

        return view('appointments.form', [
            'appointment' => $appointment,
            'patients' => $this->availablePatients($request->user()),
            'facilities' => $this->availableFacilities($request->user()),
            'doctors' => $this->availableDoctors($request->user()),
            'formAction' => route('appointments.update', $appointment),
        ]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        $data = $request->validated();
        $this->validateAssignment($request, $data);
        $this->ensureSlotAvailable($data, $appointment->id);
        $appointment->update($data);
        $this->audit($request, $appointment, 'appointment.updated');

        return redirect()->route('appointments.show', $appointment)->with('status', 'Appointment updated successfully.');
    }

    private function availablePatients($user)
    {
        $query = Patient::active()->orderBy('name');
        if ($user->hasRole('receptionist')) {
            $query->whereIn('registered_at_facility_id', $user->facilities()->pluck('facilities.id'));
        }

        return $query->get();
    }

    private function availableFacilities($user)
    {
        return $user->hasRole('admin')
            ? Facility::active()->orderBy('name')->get()
            : $user->facilities()->where('facilities.status', 'active')->orderBy('name')->get();
    }

    private function availableDoctors($user)
    {
        $query = User::active()->whereHas('role', fn ($roles) => $roles->where('key', 'doctor'))->with('facilities')->orderBy('name');
        if (! $user->hasRole('admin')) {
            $query->whereHas('facilities', fn ($facilities) => $facilities->whereIn('facilities.id', $user->facilities()->pluck('facilities.id')));
        }

        return $query->get();
    }

    private function validateAssignment(Request $request, array $data): void
    {
        $user = $request->user();
        abort_unless($user->hasRole('admin') || $user->facilities()->whereKey($data['facility_id'])->exists(), 403);
        abort_unless(User::whereKey($data['doctor_id'])->whereHas('role', fn ($roles) => $roles->where('key', 'doctor'))->whereHas('facilities', fn ($facilities) => $facilities->whereKey($data['facility_id']))->exists(), 422, 'The selected doctor is not assigned to this facility.');
        abort_unless(Patient::whereKey($data['patient_id'])->exists(), 422, 'The selected patient does not exist.');
    }

    private function ensureSlotAvailable(array $data, ?int $ignoreAppointmentId = null): void
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $query = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('scheduled_at', $scheduledAt)
            ->whereNotIn('status', ['cancelled', 'no-show']);

        if ($ignoreAppointmentId) {
            $query->where('id', '!=', $ignoreAppointmentId);
        }

        abort_if($query->exists(), 422, 'The selected doctor already has an appointment at this time.');
    }

    private function audit(Request $request, Appointment $appointment, string $action): void
    {
        AuditLog::create([
            'actor_id' => $request->user()->id,
            'facility_id' => $appointment->facility_id,
            'action' => $action,
            'auditable_type' => Appointment::class,
            'auditable_id' => $appointment->id,
            'outcome' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
