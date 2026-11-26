<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckNotSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->is_suspended) {
            $alasan = $user->alasan_suspend ?: 'Pelanggaran tata tertib / SOP laboratorium.';
            
            return redirect()->route('home')->withErrors([
                'suspended' => "Akses pengajuan ditolak karena akun Anda sedang ditangguhkan (suspend) oleh Laboran dengan alasan: {$alasan}. Silakan hubungi koordinator laboratorium kampus.",
            ]);
        }

        return $next($request);
    }
}
