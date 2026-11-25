<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $query = AuditLog::with(['actor', 'facility'])->latest();
        if ($request->filled('actor_id')) $query->where('actor_id', $request->integer('actor_id'));
        if ($request->filled('facility_id')) $query->where('facility_id', $request->integer('facility_id'));
        if ($request->filled('action')) $query->where('action', 'like', '%'.$request->string('action')->toString().'%');
        if ($request->filled('outcome')) $query->outcome($request->string('outcome')->toString());
        if ($request->filled('date')) $query->whereDate('created_at', $request->date('date'));

        return view('admin.audit.index', [
            'logs' => $query->paginate(20)->withQueryString(),
            'actors' => User::whereHas('auditLogs')->orderBy('name')->get(),
            'facilities' => Facility::orderBy('name')->get(),
            'filters' => $request->only(['actor_id', 'facility_id', 'action', 'outcome', 'date']),
        ]);
    }
}
