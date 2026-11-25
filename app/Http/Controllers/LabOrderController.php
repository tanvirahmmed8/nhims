<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabOrderRequest;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\LabOrder;
use App\Models\LabTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LabOrderController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', LabOrder::class);
        $query = LabOrder::with(['patient', 'doctor', 'facility'])->latest('requested_at');
        $user = $request->user();
        if ($user->hasRole('doctor')) $query->where('doctor_id', $user->id);
        if ($user->hasRole('lab_staff')) $query->whereIn('facility_id', $user->facilities()->pluck('facilities.id'));
        if ($user->hasRole('patient')) $query->where('patient_id', $user->patient_id ?? 0);

        return view('laboratory.index', ['orders' => $query->paginate(12)]);
    }

    public function create(Consultation $consultation): View
    {
        $this->authorize('create', LabOrder::class);
        abort_unless(auth()->id() === $consultation->doctor_id, 403);
        return view('laboratory.form', ['consultation' => $consultation->load('patient'), 'tests' => LabTest::where('active', true)->orderBy('name')->get()]);
    }

    public function store(StoreLabOrderRequest $request, Consultation $consultation): RedirectResponse
    {
        $this->authorize('create', LabOrder::class);
        abort_unless($request->user()->id === $consultation->doctor_id, 403);
        $data = $request->validated();
        $testIds = $data['test_ids'];
        unset($data['test_ids']);
        $order = DB::transaction(function () use ($request, $consultation, $data, $testIds): LabOrder {
            $order = $consultation->labOrders()->create(array_merge($data, [
                'patient_id' => $consultation->patient_id,
                'facility_id' => $consultation->facility_id,
                'doctor_id' => $consultation->doctor_id,
                'status' => 'requested',
                'requested_at' => now(),
            ]));
            foreach ($testIds as $testId) {
                $order->items()->create(['lab_test_id' => $testId, 'status' => 'requested']);
            }
            $this->audit($request, $order, 'lab_order.created');

            return $order;
        });

        return redirect()->route('laboratory.show', $order)->with('status', 'Laboratory order created successfully.');
    }

    public function show(Request $request, LabOrder $order): View
    {
        $this->authorize('view', $order);
        $order->load(['patient', 'doctor', 'facility', 'consultation', 'items.test', 'items.result.enteredBy']);
        $this->audit($request, $order, 'lab_order.viewed');
        return view('laboratory.show', compact('order'));
    }

    private function audit(Request $request, LabOrder $order, string $action): void
    {
        AuditLog::create(['actor_id' => $request->user()->id, 'facility_id' => $order->facility_id, 'action' => $action, 'auditable_type' => LabOrder::class, 'auditable_id' => $order->id, 'outcome' => 'success', 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
