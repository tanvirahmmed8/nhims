<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDispensingRequest;
use App\Models\AuditLog;
use App\Models\Dispensing;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DispensingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Dispensing::class);
        $query = Prescription::with(['patient', 'doctor', 'facility'])->whereIn('status', ['issued', 'partially_dispensed'])->latest('issued_at');
        if ($request->user()->hasRole('pharmacy_staff')) $query->whereIn('facility_id', $request->user()->facilities()->pluck('facilities.id'));
        if ($request->filled('status')) $query->where('status', $request->string('status')->toString());
        if ($request->filled('date')) $query->whereDate('issued_at', $request->date('date'));
        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->whereHas('patient', fn ($patients) => $patients->search($term));
        }
        return view('pharmacy.index', ['prescriptions' => $query->paginate(12)->withQueryString(), 'filters' => $request->only(['status', 'date', 'search'])]);
    }

    public function create(Prescription $prescription): View
    {
        $this->authorize('create', Dispensing::class);
        abort_unless(auth()->user()->facilities()->whereKey($prescription->facility_id)->exists(), 403);
        abort_if($prescription->status === 'cancelled', 422, 'Cancelled prescriptions cannot be dispensed.');
        return view('pharmacy.form', ['prescription' => $prescription->load(['patient', 'doctor', 'items'])]);
    }

    public function store(StoreDispensingRequest $request, Prescription $prescription): RedirectResponse
    {
        $this->authorize('create', Dispensing::class);
        abort_unless($request->user()->facilities()->whereKey($prescription->facility_id)->exists(), 403);
        abort_if($prescription->status === 'cancelled', 422, 'Cancelled prescriptions cannot be dispensed.');
        $data = $request->validated();
        $items = collect($data['items'])->filter(fn ($item) => (int) $item['quantity'] > 0)->values();
        abort_if($items->isEmpty(), 422, 'At least one dispensed quantity is required.');
        DB::transaction(function () use ($request, $prescription, $data, $items): void {
            $dispensing = Dispensing::create([
                'prescription_id' => $prescription->id,
                'patient_id' => $prescription->patient_id,
                'facility_id' => $prescription->facility_id,
                'pharmacy_user_id' => $request->user()->id,
                'status' => 'complete',
                'dispensed_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);
            $dispensing->items()->createMany($items->all());
            $prescription->update(['status' => 'dispensed']);
            AuditLog::create([
                'actor_id' => $request->user()->id,
                'facility_id' => $prescription->facility_id,
                'action' => 'prescription.dispensed',
                'auditable_type' => Prescription::class,
                'auditable_id' => $prescription->id,
                'outcome' => 'success',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        });

        return redirect()->route('pharmacy.index')->with('status', 'Prescription dispensed successfully.');
    }
}
