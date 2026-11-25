<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabResultRequest;
use App\Models\AuditLog;
use App\Models\LabOrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LabResultController extends Controller
{
    public function create(LabOrderItem $item): View
    {
        abort_unless(auth()->user()->hasRole('lab_staff'), 403);
        $item->load(['order.patient', 'order.facility', 'test']);
        abort_unless(auth()->user()->facilities()->whereKey($item->order->facility_id)->exists(), 403);
        return view('laboratory.result', compact('item'));
    }

    public function store(StoreLabResultRequest $request, LabOrderItem $item): RedirectResponse
    {
        $item->load('order');
        abort_unless(auth()->user()->facilities()->whereKey($item->order->facility_id)->exists(), 403);
        $result = $item->result()->updateOrCreate([], array_merge($request->validated(), ['patient_id' => $item->order->patient_id, 'entered_by' => $request->user()->id, 'status' => 'published', 'published_at' => now()]));
        $item->update(['status' => 'completed']);
        $item->order()->update(['status' => 'completed']);
        AuditLog::create(['actor_id' => $request->user()->id, 'facility_id' => $item->order->facility_id, 'action' => 'lab_result.published', 'auditable_type' => get_class($result), 'auditable_id' => $result->id, 'outcome' => 'success', 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);

        return redirect()->route('laboratory.show', $item->order)->with('status', 'Laboratory result published.');
    }
}
