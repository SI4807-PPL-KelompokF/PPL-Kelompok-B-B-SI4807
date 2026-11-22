<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminRole
{
    /**
     * Pastikan hanya pengguna dengan peran "admin" yang dapat mengakses rute dasbor analitik.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * Pastikan pengguna terautentikasi memiliki peran 'admin'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            $message = 'Akses ditolak. Fitur ini hanya dapat diakses oleh Administrator.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            abort(403, $message);
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Akses ditolak. Halaman ini hanya dapat diakses oleh Administrator.',
                ], 403);
            }

            abort(403, 'Akses ditolak. Halaman ini hanya dapat diakses oleh Administrator.');
        }

        return $next($request);
    }
}
