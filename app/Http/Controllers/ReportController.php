<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dispensing;
use App\Models\Facility;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $facilityId = $request->integer('facility_id') ?: null;
        $date = $request->date('date');
        $facilityScope = fn ($query) => $facilityId ? $query->where('facility_id', $facilityId) : $query;
        $dateScope = fn ($query, string $column = 'created_at') => $date ? $query->whereDate($column, $date) : $query;

        $patientQuery = Patient::query();
        if ($facilityId) $patientQuery->where('registered_at_facility_id', $facilityId);
        if ($date) $patientQuery->whereDate('created_at', $date);

        $appointmentQuery = $facilityScope(Appointment::query());
        $dateScope($appointmentQuery, 'scheduled_at');
        $labQuery = $facilityScope(LabOrder::query());
        $dateScope($labQuery, 'requested_at');
        $prescriptionQuery = $facilityScope(Prescription::query());
        $dateScope($prescriptionQuery, 'issued_at');
        $dispensingQuery = $facilityScope(Dispensing::query());
        $dateScope($dispensingQuery, 'dispensed_at');

        return view('admin.reports.index', [
            'facilities' => Facility::active()->orderBy('name')->get(),
            'filters' => ['facility_id' => $facilityId, 'date' => $date?->format('Y-m-d')],
            'metrics' => [
                'patients' => $patientQuery->count(),
                'appointments' => $appointmentQuery->count(),
                'pendingLabs' => (clone $labQuery)->whereIn('status', ['requested', 'collected', 'processing'])->count(),
                'prescriptions' => $prescriptionQuery->count(),
                'dispensed' => $dispensingQuery->whereIn('status', ['partial', 'complete'])->count(),
            ],
            'appointmentStatuses' => (clone $appointmentQuery)->selectRaw('status, COUNT(*) as total')->groupBy('status')->orderBy('status')->get(),
            'facilitySummary' => Facility::withCount(['patients', 'appointments'])->when($facilityId, fn ($facilities) => $facilities->whereKey($facilityId))->orderBy('name')->get(),
        ]);
    }
}
