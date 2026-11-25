<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()?->hasAnyRole($roles)) {
            AuditLog::create([
                'actor_id' => $request->user()?->id,
                'facility_id' => $request->user()?->facilities()->wherePivot('is_primary', true)->value('facilities.id'),
                'action' => 'access.denied',
                'outcome' => 'denied',
                'metadata' => ['route' => $request->path(), 'required_roles' => $roles],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            abort(403, 'You are not authorized to access this area.');
        }

        return $next($request);
    }
}
