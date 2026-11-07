<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureLaboranRole
{
    /**
     * Pastikan hanya pengguna dengan peran "laboran" yang dapat mengakses rute kerja Laboran.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->isLaboran()) {
            $message = 'Akses ditolak. Fitur ini hanya dapat diakses oleh petugas Laboran.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
