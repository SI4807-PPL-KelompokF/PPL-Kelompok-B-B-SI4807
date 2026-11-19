<?php

namespace App\Http\Controllers;

use App\Models\MesinLab;
use Illuminate\Http\Request;

class AdminMesinLabController extends Controller
{
    // Daftar semua mesin
    public function index()
    {
        $mesins = MesinLab::latest()->get();

        return view('admin.mesin.index', compact('mesins'));
    }

    // Form tambah mesin
    public function create()
    {
        return view('admin.mesin.create');
    }

    // Simpan mesin baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_mesin'       => 'required|string|max:150',
            'kategori_layanan' => 'required|string|max:100',
        ]);

        MesinLab::create($data);

        return redirect()->route('admin.mesin.index')
            ->with('success', 'Mesin berhasil ditambahkan.');
    }

    // Form ubah mesin
    public function edit(MesinLab $mesin)
    {
        return view('admin.mesin.edit', compact('mesin'));
    }

    // Simpan perubahan mesin
    public function update(Request $request, MesinLab $mesin)
    {
        $data = $request->validate([
            'nama_mesin'       => 'required|string|max:150',
            'kategori_layanan' => 'required|string|max:100',
        ]);

        $mesin->update($data);

        return redirect()->route('admin.mesin.index')
            ->with('success', 'Mesin berhasil diperbarui.');
    }

    // Toggle status Aktif <-> Maintenance
    public function toggleStatus(MesinLab $mesin)
    {
        $mesin->update(['status_aktif' => ! $mesin->status_aktif]);

        return back()->with('success', 'Status mesin berhasil diubah.');
    }

    // Hapus mesin
    public function destroy(MesinLab $mesin)
    {
        $mesin->delete();

        return redirect()->route('admin.mesin.index')
            ->with('success', 'Mesin berhasil dihapus.');
    }
}