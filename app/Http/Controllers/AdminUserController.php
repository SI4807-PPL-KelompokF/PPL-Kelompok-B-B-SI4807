<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Tampilkan halaman Manajemen Pengguna oleh Admin.
     */
    public function index()
    {
        $users = User::orderBy('id')->get();

        $counts = [
            'all' => $users->count(),
            'admin' => $users->where('role', User::ROLE_ADMIN)->count(),
            'laboran' => $users->where('role', User::ROLE_LABORAN)->count(),
            'pemohon' => $users->filter(function (User $u) {
                return $u->isPemohon();
            })->count(),
        ];

        $usersPayload = $users->map(function (User $u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role ?? User::ROLE_PEMOHON,
                'role_label' => $u->roleLabel(),
                'chip_class' => $u->roleChipClass(),
                'joined' => $u->created_at ? $u->created_at->format('d M Y') : '-',
                'update_url' => route('admin.users.update', $u->id),
                'destroy_url' => route('admin.users.destroy', $u->id),
            ];
        })->values()->all();

        return view('admin.users', [
            'users' => $users,
            'counts' => $counts,
            'usersPayload' => $usersPayload,
        ]);
    }

    /**
     * Tambahkan pengguna baru.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Akun pengguna berhasil ditambahkan.');
    }

    /**
     * Perbarui data atau peran pengguna.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if ($user->id === $request->user()->id && $validated['role'] !== User::ROLE_ADMIN) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['role' => 'Peran akun Administrator yang sedang aktif tidak dapat diubah.']);
        }

        $payload = [
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['delete' => 'Tidak dapat menghapus akun yang sedang digunakan.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Akun pengguna berhasil dihapus.');
    }
}
