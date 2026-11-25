<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Prescription;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'active']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user()->loadMissing('role');
        $isPatient = $user->role?->key === 'patient' && $user->patient_id !== null;

        $patientFilter = static function ($query) use ($isPatient, $user): void {
            if ($isPatient) {
                $query->where('patient_id', $user->patient_id);
            }
        };

        $appointmentsTodayQuery = Appointment::query()->whereDate('scheduled_at', today());
        $patientFilter($appointmentsTodayQuery);

        $pendingLabsQuery = LabOrder::pending();
        $patientFilter($pendingLabsQuery);

        $activePrescriptionsQuery = Prescription::query()->whereIn('status', ['issued', 'partially_dispensed']);
        $patientFilter($activePrescriptionsQuery);

        $upcomingAppointmentsQuery = Appointment::with(['patient', 'doctor', 'facility'])
            ->upcoming()
            ->limit(5);
        $patientFilter($upcomingAppointmentsQuery);

        return view('home', [
            'metrics' => [
                'patients' => $isPatient ? 1 : Patient::count(),
                'appointmentsToday' => $appointmentsTodayQuery->count(),
                'pendingLabs' => $pendingLabsQuery->count(),
                'activePrescriptions' => $activePrescriptionsQuery->count(),
            ],
            'upcomingAppointments' => $upcomingAppointmentsQuery->get(),
            'recentAuditLogs' => $isPatient ? collect() : AuditLog::with(['actor', 'facility'])->latest()->limit(5)->get(),
            'facilitySummary' => $isPatient ? collect() : Facility::withCount(['patients', 'appointments'])->orderBy('name')->get(),
            'isPatient' => $isPatient,
        ]);
    }
}
