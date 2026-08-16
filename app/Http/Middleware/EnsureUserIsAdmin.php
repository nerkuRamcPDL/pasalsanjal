<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            \App\Models\AuditLog::record(
                $user?->id,
                'blocked_admin_access',
                'security',
            );

            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
