<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminFakultasProdiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin'])->except(['getProdiByFakultas']);
    }

    /**
     * PBI-03 (UC-04): Tampilkan halaman kelola Master Data Fakultas & Program Studi.
     */
    public function index(Request $request)
    {
        // Pastikan tabel memiliki data bawaan Telkom University jika masih kosong
        if (Schema::hasTable('fakultas')) {
            Fakultas::seedDefaultData();
        }

        $searchFakultas = trim($request->query('search_fakultas', ''));
        $searchProdi    = trim($request->query('search_prodi', ''));
        $filterFakultas = $request->query('filter_fakultas_id', '');

        // Query Master Fakultas
        $fakultasQuery = Fakultas::withCount('prodi');
        if (!empty($searchFakultas)) {
            $fakultasQuery->where(function ($q) use ($searchFakultas) {
                $q->where('nama_fakultas', 'like', "%{$searchFakultas}%")
                  ->orWhere('kode_fakultas', 'like', "%{$searchFakultas}%");
            });
        }
        $fakultas = $fakultasQuery->orderBy('id', 'asc')
            ->paginate(10, ['*'], 'page_fakultas')
            ->withQueryString();

        // Query Master Program Studi
        $prodiQuery = Prodi::with('fakultas');
        if (!empty($searchProdi)) {
            $prodiQuery->where(function ($q) use ($searchProdi) {
                $q->where('nama_prodi', 'like', "%{$searchProdi}%")
                  ->orWhere('kode_prodi', 'like', "%{$searchProdi}%");
            });
        }
        if (!empty($filterFakultas)) {
            $prodiQuery->where('fakultas_id', $filterFakultas);
        }
        $prodi = $prodiQuery->orderBy('fakultas_id', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(10, ['*'], 'page_prodi')
            ->withQueryString();

        $totalFakultas = Fakultas::count();
        $totalProdi    = Prodi::count();
        $allFakultas   = Fakultas::orderBy('nama_fakultas', 'asc')->get();

        return view('admin.master-fakultas-prodi', compact(
            'fakultas',
            'prodi',
            'totalFakultas',
            'totalProdi',
            'allFakultas',
            'searchFakultas',
            'searchProdi',
            'filterFakultas'
        ));
    }

    /**
     * Simpan data Fakultas baru.
     */
    public function storeFakultas(Request $request)
    {
        $validated = $request->validate([
            'kode_fakultas' => ['required', 'string', 'max:30', 'unique:fakultas,kode_fakultas'],
            'nama_fakultas' => ['required', 'string', 'max:150', 'unique:fakultas,nama_fakultas'],
        ], [
            'kode_fakultas.required' => 'Kode fakultas wajib diisi.',
            'kode_fakultas.unique'   => 'Kode fakultas sudah terdaftar dalam sistem.',
            'nama_fakultas.required' => 'Nama fakultas wajib diisi.',
            'nama_fakultas.unique'   => 'Nama fakultas sudah terdaftar dalam sistem.',
        ]);

        $fakultas = Fakultas::create([
            'kode_fakultas' => strtoupper(trim($validated['kode_fakultas'])),
            'nama_fakultas' => trim($validated['nama_fakultas']),
        ]);

        return redirect()
            ->route('admin.master.fakultas-prodi.index')
            ->with('status', "Fakultas {$fakultas->nama_fakultas} ({$fakultas->kode_fakultas}) berhasil ditambahkan.");
    }

    /**
     * Perbarui data Fakultas.
     */
    public function updateFakultas(Request $request, Fakultas $fakultas)
    {
        $validated = $request->validate([
            'kode_fakultas' => [
                'required', 'string', 'max:30',
                Rule::unique('fakultas', 'kode_fakultas')->ignore($fakultas->id),
            ],
            'nama_fakultas' => [
                'required', 'string', 'max:150',
                Rule::unique('fakultas', 'nama_fakultas')->ignore($fakultas->id),
            ],
        ], [
            'kode_fakultas.required' => 'Kode fakultas wajib diisi.',
            'kode_fakultas.unique'   => 'Kode fakultas sudah terdaftar.',
            'nama_fakultas.required' => 'Nama fakultas wajib diisi.',
            'nama_fakultas.unique'   => 'Nama fakultas sudah terdaftar.',
        ]);

        $fakultas->update([
            'kode_fakultas' => strtoupper(trim($validated['kode_fakultas'])),
            'nama_fakultas' => trim($validated['nama_fakultas']),
        ]);

        return redirect()
            ->route('admin.master.fakultas-prodi.index')
            ->with('status', "Data Fakultas {$fakultas->nama_fakultas} berhasil diperbarui.");
    }

    /**
     * Hapus data Fakultas secara aman.
     */
    public function destroyFakultas(Fakultas $fakultas)
    {
        // 1. Cek apakah masih memiliki Prodi
        $jumlahProdi = $fakultas->prodi()->count();
        if ($jumlahProdi > 0) {
            return back()->withErrors([
                'fakultas' => "Fakultas {$fakultas->nama_fakultas} tidak dapat dihapus karena masih menaungi {$jumlahProdi} program studi. Hapus atau pindahkan program studi terlebih dahulu.",
            ]);
        }

        // 2. Cek apakah terhubung dengan akun users
        $terhubungUser = User::where('fakultas_id', $fakultas->id)
            ->orWhere('fakultas_id', $fakultas->nama_fakultas)
            ->orWhere('fakultas_id', $fakultas->kode_fakultas)
            ->exists();

        if ($terhubungUser) {
            return back()->withErrors([
                'fakultas' => "Fakultas {$fakultas->nama_fakultas} tidak dapat dihapus karena masih digunakan sebagai data profil akademik pengguna/pemohon.",
            ]);
        }

        // 3. Cek tiket pengujian jika ada
        if (Schema::hasTable('tiket_pengujian')) {
            $terhubungTiket = TiketPengujian::where('fakultas', $fakultas->nama_fakultas)
                ->orWhere('fakultas', $fakultas->kode_fakultas)
                ->exists();

            if ($terhubungTiket) {
                return back()->withErrors([
                    'fakultas' => "Fakultas {$fakultas->nama_fakultas} tidak dapat dihapus karena tercatat dalam riwayat pengujian sampel laboratorium.",
                ]);
            }
        }

        $namaFakultas = $fakultas->nama_fakultas;
        $fakultas->delete();

        return redirect()
            ->route('admin.master.fakultas-prodi.index')
            ->with('status', "Fakultas {$namaFakultas} berhasil dihapus dari master data.");
    }

    /**
     * Simpan data Program Studi baru.
     */
    public function storeProdi(Request $request)
    {
        $validated = $request->validate([
            'fakultas_id' => ['required', 'exists:fakultas,id'],
            'kode_prodi'  => ['nullable', 'string', 'max:30'],
            'nama_prodi'  => ['required', 'string', 'max:150'],
        ], [
            'fakultas_id.required' => 'Fakultas induk wajib dipilih.',
            'fakultas_id.exists'   => 'Fakultas yang dipilih tidak valid.',
            'nama_prodi.required'  => 'Nama program studi wajib diisi.',
        ]);

        $prodi = Prodi::create([
            'fakultas_id' => $validated['fakultas_id'],
            'kode_prodi'  => !empty($validated['kode_prodi']) ? strtoupper(trim($validated['kode_prodi'])) : null,
            'nama_prodi'  => trim($validated['nama_prodi']),
        ]);

        return redirect()
            ->route('admin.master.fakultas-prodi.index')
            ->with('status', "Program Studi {$prodi->nama_prodi} berhasil ditambahkan.");
    }

    /**
     * Perbarui data Program Studi.
     */
    public function updateProdi(Request $request, Prodi $prodi)
    {
        $validated = $request->validate([
            'fakultas_id' => ['required', 'exists:fakultas,id'],
            'kode_prodi'  => ['nullable', 'string', 'max:30'],
            'nama_prodi'  => ['required', 'string', 'max:150'],
        ], [
            'fakultas_id.required' => 'Fakultas induk wajib dipilih.',
            'fakultas_id.exists'   => 'Fakultas yang dipilih tidak valid.',
            'nama_prodi.required'  => 'Nama program studi wajib diisi.',
        ]);

        $prodi->update([
            'fakultas_id' => $validated['fakultas_id'],
            'kode_prodi'  => !empty($validated['kode_prodi']) ? strtoupper(trim($validated['kode_prodi'])) : null,
            'nama_prodi'  => trim($validated['nama_prodi']),
        ]);

        return redirect()
            ->route('admin.master.fakultas-prodi.index')
            ->with('status', "Program Studi {$prodi->nama_prodi} berhasil diperbarui.");
    }

    /**
     * Hapus data Program Studi secara aman.
     */
    public function destroyProdi(Prodi $prodi)
    {
        // 1. Cek apakah terhubung dengan akun users
        $terhubungUser = User::where(function ($q) use ($prodi) {
            $q->where('prodi_id', $prodi->id)
              ->orWhere('prodi_id', $prodi->nama_prodi);
            if (!empty($prodi->kode_prodi)) {
                $q->orWhere('prodi_id', $prodi->kode_prodi);
            }
        })->exists();

        if ($terhubungUser) {
            return back()->withErrors([
                'prodi' => "Program Studi {$prodi->nama_prodi} tidak dapat dihapus karena masih digunakan sebagai data profil akademik pengguna/pemohon.",
            ]);
        }

        // 2. Cek apakah terhubung dengan riwayat tiket pengujian
        if (Schema::hasTable('tiket_pengujian')) {
            $terhubungTiket = TiketPengujian::where(function ($q) use ($prodi) {
                $q->where('prodi', $prodi->nama_prodi);
                if (!empty($prodi->kode_prodi)) {
                    $q->orWhere('prodi', $prodi->kode_prodi);
                }
            })->exists();

            if ($terhubungTiket) {
                return back()->withErrors([
                    'prodi' => "Program Studi {$prodi->nama_prodi} tidak dapat dihapus karena tercatat dalam riwayat pengujian sampel laboratorium.",
                ]);
            }
        }

        $namaProdi = $prodi->nama_prodi;
        $prodi->delete();

        return redirect()
            ->route('admin.master.fakultas-prodi.index')
            ->with('status', "Program Studi {$namaProdi} berhasil dihapus dari master data.");
    }

    /**
     * Endpoint API JSON untuk mengambil daftar prodi dinamis berdasarkan Fakultas.
     */
    public function getProdiByFakultas(Request $request, $fakultasId = null)
    {
        $id = $fakultasId ?: $request->input('fakultas_id');

        if (empty($id)) {
            return response()->json(['ok' => false, 'prodis' => []]);
        }

        // Bisa cari berdasarkan id angka atau nama/kode fakultas
        $fakultas = is_numeric($id)
            ? Fakultas::find($id)
            : Fakultas::where('nama_fakultas', $id)
                ->orWhere('kode_fakultas', $id)
                ->orWhere('nama_fakultas', 'like', "%{$id}%")
                ->first();

        if (!$fakultas) {
            return response()->json(['ok' => false, 'prodis' => []]);
        }

        $prodis = $fakultas->prodi()->orderBy('nama_prodi')->get(['id', 'kode_prodi', 'nama_prodi']);

        return response()->json([
            'ok'            => true,
            'fakultas_id'   => $fakultas->id,
            'nama_fakultas' => $fakultas->nama_fakultas,
            'prodis'        => $prodis,
        ]);
    }
}
