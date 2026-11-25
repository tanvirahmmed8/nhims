<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Prescription::class);
        $query = Prescription::with(['patient', 'doctor', 'facility'])->latest('issued_at');
        $user = $request->user();
        if ($user->hasRole('doctor')) $query->where('doctor_id', $user->id);
        if ($user->hasRole('pharmacy_staff')) $query->whereIn('facility_id', $user->facilities()->pluck('facilities.id'))->whereIn('status', ['issued', 'partially_dispensed']);
        if ($user->hasRole('patient')) $query->where('patient_id', $user->patient_id ?? 0);

        return view('prescriptions.index', ['prescriptions' => $query->paginate(12)]);
    }

    public function create(Consultation $consultation): View
    {
        $this->authorize('create', Prescription::class);
        abort_unless(auth()->id() === $consultation->doctor_id, 403);
        $consultation->load('patient');
        return view('prescriptions.form', compact('consultation'));
    }

    public function store(StorePrescriptionRequest $request, Consultation $consultation): RedirectResponse
    {
        $this->authorize('create', Prescription::class);
        abort_unless($request->user()->id === $consultation->doctor_id, 403);
        $prescription = DB::transaction(function () use ($request, $consultation): Prescription {
            $prescription = $consultation->prescriptions()->create([
                'patient_id' => $consultation->patient_id,
                'facility_id' => $consultation->facility_id,
                'doctor_id' => $consultation->doctor_id,
                'status' => 'issued',
                'issued_at' => now(),
            ]);
            $prescription->items()->createMany($request->validated()['items']);
            $this->audit($request, $prescription, 'prescription.created');

            return $prescription;
        });

        return redirect()->route('prescriptions.show', $prescription)->with('status', 'Prescription issued successfully.');
    }

    public function show(Request $request, Prescription $prescription): View
    {
        $this->authorize('view', $prescription);
        $prescription->load(['patient', 'doctor', 'facility', 'consultation', 'items', 'dispensings.items']);
        $this->audit($request, $prescription, 'prescription.viewed');
        return view('prescriptions.show', compact('prescription'));
    }

    private function audit(Request $request, Prescription $prescription, string $action): void
    {
        AuditLog::create(['actor_id' => $request->user()->id, 'facility_id' => $prescription->facility_id, 'action' => $action, 'auditable_type' => Prescription::class, 'auditable_id' => $prescription->id, 'outcome' => 'success', 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
