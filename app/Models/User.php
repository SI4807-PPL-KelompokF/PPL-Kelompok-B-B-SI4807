<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_LABORAN = 'laboran';
    public const ROLE_PEMOHON = 'pemohon';

    public const ALLOWED_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_LABORAN,
        self::ROLE_PEMOHON,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nama_lengkap',
        'email',
        'password',
        'role',
        'nomor_induk',
        'fakultas_id',
        'prodi_id',
        'foto_identitas',
        'status_validasi_ktm',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return ($this->role ?? self::ROLE_PEMOHON) === self::ROLE_ADMIN;
    }

    public function isLaboran(): bool
    {
        return ($this->role ?? self::ROLE_PEMOHON) === self::ROLE_LABORAN;
    }

    public function isPemohon(): bool
    {
        return !$this->isAdmin() && !$this->isLaboran();
    }

    public function roleLabel(): string
    {
        if ($this->isAdmin()) {
            return 'Admin';
        }

        if ($this->isLaboran()) {
            return 'Laboran';
        }

        return 'Pemohon';
    }

    public function roleChipClass(): string
    {
        if ($this->isAdmin()) {
            return 'tu-chip--jasmine';
        }

        if ($this->isLaboran()) {
            return 'lu-chip--flame';
        }

        return 'tu-chip--cream';
    }

    /**
     * Rute beranda utama sesuai peran pengguna.
     */
    public function homeRoute(): string
    {
        if ($this->isAdmin() && Route::has('admin.users.index')) {
            return route('admin.users.index');
        }

        if ($this->isLaboran() && Route::has('laboran.pengujian.index')) {
            return route('laboran.pengujian.index');
        }

        return route('home');
    }

    public function tiketPengujian()
    {
        return $this->hasMany(TiketPengujian::class, 'user_id');
    }

    /**
     * Cek apakah profil akademik pemohon sudah tervalidasi.
     */
    public function isValidasiKtm(): bool
    {
        return ($this->status_validasi_ktm ?? 'Belum Tervalidasi') === 'Tervalidasi';
    }

    /**
     * Cek apakah validasi KTM pemohon ditolak oleh Laboran.
     */
    public function isKtmDitolak(): bool
    {
        return ($this->status_validasi_ktm ?? '') === 'Ditolak';
    }

    /**
     * Cek apakah data profil wajib PBI-01 sudah dilengkapi.
     */
    public function isProfilLengkap(): bool
    {
        return !empty($this->nama_lengkap)
            && !empty($this->nomor_induk)
            && !empty($this->fakultas_id)
            && !empty($this->prodi_id);
    }

    /**
     * Relasi Eloquent ke Fakultas (PBI-03).
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    /**
     * Relasi Eloquent ke Program Studi (PBI-03).
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
