<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\UpdateConsultationRequest;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Consultation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasAnyRole(['admin', 'doctor']), 403);
        $query = Appointment::with(['patient', 'facility', 'consultation'])->orderBy('scheduled_at');
        if ($request->user()->hasRole('doctor')) {
            $query->where('doctor_id', $request->user()->id);
        }

        return view('consultations.index', ['appointments' => $query->paginate(12)]);
    }

    public function create(Appointment $appointment): View
    {
        $this->authorize('create', Consultation::class);
        abort_unless(auth()->id() === $appointment->doctor_id, 403);
        $appointment->load(['patient.appointments', 'patient.consultations', 'patient.prescriptions', 'patient.labOrders', 'doctor', 'facility']);

        return view('consultations.form', compact('appointment'));
    }

    public function store(StoreConsultationRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('create', Consultation::class);
        abort_unless($request->user()->id === $appointment->doctor_id, 403);
        abort_if($appointment->consultation()->exists(), 422, 'A consultation already exists for this appointment.');
        $consultation = DB::transaction(function () use ($request, $appointment): Consultation {
            $consultation = $appointment->consultation()->create(array_merge($request->validated(), [
                'patient_id' => $appointment->patient_id,
                'facility_id' => $appointment->facility_id,
                'doctor_id' => $appointment->doctor_id,
            ]));
            if ($consultation->status === 'completed') {
                $appointment->update(['status' => 'completed']);
            }
            $this->audit($request, $consultation, 'consultation.created');

            return $consultation;
        });

        return redirect()->route('consultations.show', $consultation)->with('status', 'Consultation saved successfully.');
    }

    public function show(Request $request, Consultation $consultation): View
    {
        $this->authorize('view', $consultation);
        $consultation->load(['patient', 'doctor', 'facility', 'appointment', 'prescriptions.items', 'labOrders.items.test', 'labOrders.items.result']);
        $this->audit($request, $consultation, 'consultation.viewed');

        return view('consultations.show', compact('consultation'));
    }

    public function edit(Consultation $consultation): View
    {
        $this->authorize('update', $consultation);
        return view('consultations.form', ['appointment' => $consultation->appointment->load('patient'), 'consultation' => $consultation]);
    }

    public function update(UpdateConsultationRequest $request, Consultation $consultation): RedirectResponse
    {
        $this->authorize('update', $consultation);
        $consultation->update($request->validated());
        if ($consultation->status === 'completed') {
            $consultation->appointment()->update(['status' => 'completed']);
        }
        $this->audit($request, $consultation, 'consultation.updated');

        return redirect()->route('consultations.show', $consultation)->with('status', 'Consultation updated successfully.');
    }

    private function audit(Request $request, Consultation $consultation, string $action): void
    {
        AuditLog::create(['actor_id' => $request->user()->id, 'facility_id' => $consultation->facility_id, 'action' => $action, 'auditable_type' => Consultation::class, 'auditable_id' => $consultation->id, 'outcome' => 'success', 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
