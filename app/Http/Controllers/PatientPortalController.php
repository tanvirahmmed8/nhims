<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\View\View;

class PatientPortalController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasRole('patient') && auth()->user()->patient_id, 403);
        $patient = Patient::with(['registeredAtFacility', 'emergencyContacts'])->findOrFail(auth()->user()->patient_id);
        $appointments = Appointment::with(['doctor', 'facility'])->where('patient_id', $patient->id)->orderBy('scheduled_at')->get();
        $prescriptions = Prescription::with(['doctor', 'items', 'dispensings'])->where('patient_id', $patient->id)->latest('issued_at')->get();
        $labResults = LabResult::with(['orderItem.test', 'orderItem.order.facility'])->where('patient_id', $patient->id)->where('status', 'published')->latest('published_at')->get();

        return view('portal.index', compact('patient', 'appointments', 'prescriptions', 'labResults'));
    }
}
