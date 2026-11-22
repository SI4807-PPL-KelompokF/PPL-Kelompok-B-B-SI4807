@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden" id="au-root">
    <x-bg-pattern uid="admin-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
            <div class="tu-reveal">
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">Manajemen Pengguna</h1>
            </div>
            <div class="tu-reveal [--d:100ms]">
                <button type="button" id="btn-add-user" dusk="btn-add-user" class="tu-btn">
                    <span aria-hidden="true">+</span>
                    <span>Tambah Pengguna</span>
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="tu-alert" dusk="user-status-alert" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="border border-racing-red bg-red-50/60 px-4 py-3 text-xs font-medium text-racing-red rounded-[3px]" dusk="user-error-banner" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Metric strip -->
        <section aria-label="Ringkasan akun pengguna">
            <div class="grid grid-cols-2 lg:grid-cols-4 border-y border-gray-200 bg-white/70">
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-7 border-r border-b lg:border-b-0 border-gray-200 [--c:#111827]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-gray-600">Total Pengguna</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $counts['all'] }}</p>
                </div>
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-7 border-b lg:border-b-0 lg:border-r border-gray-200 [--c:#F7D488] [--d:80ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-amber-800">Admin</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $counts['admin'] }}</p>
                </div>
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-7 border-r border-gray-200 [--c:#F26A2D] [--d:160ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-tiger-flame">Laboran</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $counts['laboran'] }}</p>
                </div>
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-7 [--c:#34d399] [--d:240ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-emerald-800">Pemohon</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $counts['pemohon'] }}</p>
                </div>
            </div>
        </section>

        <!-- Users table -->
        <section class="bg-white border border-gray-200 tu-reveal" aria-labelledby="users-heading">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 border-b border-gray-200 px-5 sm:px-7 pt-5">
                <div>
                    <h2 id="users-heading" class="text-base font-bold tracking-tight text-gray-950">Daftar Pengguna</h2>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between lg:justify-end gap-4 lg:gap-8">
                    <div class="flex overflow-x-auto no-scrollbar" role="tablist" aria-label="Filter peran pengguna" id="user-tabs">
                        <button type="button" role="tab" class="tu-tab" data-role-filter="all" aria-selected="true">Semua <span class="tu-mono text-xs text-gray-400">{{ $counts['all'] }}</span></button>
                        <button type="button" role="tab" class="tu-tab" data-role-filter="admin" aria-selected="false">Admin <span class="tu-mono text-xs text-gray-400">{{ $counts['admin'] }}</span></button>
                        <button type="button" role="tab" class="tu-tab" data-role-filter="laboran" aria-selected="false">Laboran <span class="tu-mono text-xs text-gray-400">{{ $counts['laboran'] }}</span></button>
                        <button type="button" role="tab" class="tu-tab" data-role-filter="pemohon" aria-selected="false">Pemohon <span class="tu-mono text-xs text-gray-400">{{ $counts['pemohon'] }}</span></button>
                    </div>
                    <div class="relative pb-3 sm:w-64">
                        <svg class="pointer-events-none absolute left-3 top-[13px] h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                        <label for="user-search" class="sr-only">Cari pengguna</label>
                        <input id="user-search" dusk="search-user-input" type="search" class="tu-input !py-2 !pl-9 !text-sm" placeholder="Cari nama atau email" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table dusk="users-table" class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium">Nama</th>
                            <th scope="col" class="px-3 py-3 font-medium">Email</th>
                            <th scope="col" class="px-3 py-3 font-medium">Peran</th>
                            <th scope="col" class="hidden md:table-cell px-3 py-3 font-medium">Terdaftar</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 text-right font-medium"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody id="user-rows" class="divide-y divide-gray-100">
                        @foreach ($usersPayload as $u)
                            <tr
                                class="lu-row no-anim"
                                dusk="user-row-{{ $u['id'] }}"
                                data-role="{{ $u['role'] }}"
                                data-search="{{ strtolower($u['name'] . ' ' . $u['email'] . ' ' . $u['role_label']) }}"
                            >
                                <td class="px-5 sm:px-7 py-4 align-middle">
                                    <span class="font-semibold text-gray-950">{{ $u['name'] }}</span>
                                </td>
                                <td class="px-3 py-4 align-middle">
                                    <span class="tu-mono text-xs text-gray-600">{{ $u['email'] }}</span>
                                </td>
                                <td class="px-3 py-4 align-middle whitespace-nowrap">
                                    <span class="tu-chip {{ $u['chip_class'] }}">{{ $u['role_label'] }}</span>
                                </td>
                                <td class="hidden md:table-cell px-3 py-4 align-middle whitespace-nowrap">
                                    <span class="tu-mono text-xs text-gray-500">{{ $u['joined'] }}</span>
                                </td>
                                <td class="px-5 sm:px-7 py-3 align-middle text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            dusk="edit-user-{{ $u['id'] }}"
                                            class="tu-btn tu-btn--sm tu-btn--ghost"
                                            data-edit-id="{{ $u['id'] }}"
                                        >
                                            <span>Edit</span>
                                        </button>

                                        @if ($u['id'] !== Auth::id())
                                            <form
                                                method="POST"
                                                action="{{ $u['destroy_url'] }}"
                                                class="inline"
                                                onsubmit="return confirm('Hapus akun {{ addslashes($u['name']) }}?');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    dusk="delete-user-{{ $u['id'] }}"
                                                    class="tu-btn tu-btn--sm tu-btn--ghost text-racing-red hover:border-racing-red hover:text-racing-red"
                                                >
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p id="user-empty" class="hidden px-7 py-14 text-center text-sm text-gray-500">Tidak ada pengguna yang cocok.</p>
            </div>
        </section>
    </div>
</div>

<!-- Slide-over Drawer (Tambah / Edit Pengguna) -->
<div class="lu-overlay" id="user-overlay" aria-hidden="true"></div>
<aside class="lu-drawer" id="user-drawer" dusk="user-drawer-panel" role="dialog" aria-modal="true" aria-labelledby="user-dr-title" aria-hidden="true">
    <header class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5">
        <h2 id="user-dr-title" class="text-xl font-extrabold tracking-tight text-gray-950">Tambah Pengguna</h2>
        <button type="button" class="tu-icon-btn" id="user-dr-close" aria-label="Tutup panel">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </header>

    <form
        id="user-form"
        method="POST"
        action="{{ route('admin.users.store') }}"
        class="flex min-h-0 flex-1 flex-col"
    >
        @csrf
        <input type="hidden" name="_method" id="f-method" value="POST">

        <div class="lu-drawer-body flex-1 space-y-5 overflow-y-auto px-6 py-6">
            <div>
                <label for="f-name" class="tu-label">Nama Lengkap</label>
                <input id="f-name" dusk="input-user-name" type="text" name="name" required class="tu-input">
            </div>

            <div>
                <label for="f-email" class="tu-label">Alamat Email</label>
                <input
                    id="f-email"
                    dusk="input-user-email"
                    type="email"
                    name="email"
                    required
                    placeholder="nama@telkomuniversity.ac.id"
                    pattern="^[a-zA-Z0-9._%+-]+@([a-zA-Z0-9-]+\.)*telkomuniversity\.ac\.id$"
                    title="Gunakan alamat email berdomain @telkomuniversity.ac.id"
                    class="tu-input"
                >
            </div>

            <div>
                <label for="f-role" class="tu-label">Peran</label>
                <select id="f-role" dusk="select-user-role" name="role" required class="tu-input">
                    <option value="pemohon">Pemohon</option>
                    <option value="laboran">Laboran</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label for="f-password" class="tu-label" id="f-password-label">Kata Sandi</label>
                <input
                    id="f-password"
                    dusk="input-user-password"
                    type="password"
                    name="password"
                    minlength="8"
                    autocomplete="new-password"
                    class="tu-input"
                >
            </div>

            <div>
                <label for="f-password-confirm" class="tu-label">Konfirmasi Kata Sandi</label>
                <input
                    id="f-password-confirm"
                    dusk="input-user-password-confirm"
                    type="password"
                    name="password_confirmation"
                    minlength="8"
                    autocomplete="new-password"
                    class="tu-input"
                >
            </div>
        </div>

        <footer class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4">
            <button type="button" class="tu-btn tu-btn--ghost" id="user-dr-cancel">Batal</button>
            <button type="submit" dusk="submit-user-button" class="tu-btn">
                <span id="user-submit-label">Simpan</span>
            </button>
        </footer>
    </form>
</aside>

<script id="users-data" type="application/json">{!! json_encode($usersPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script>
(function () {
    var dataEl = document.getElementById('users-data');
    if (!dataEl) return;
    var users = [];
    try { users = JSON.parse(dataEl.textContent || '[]'); } catch (e) {}

    var byId = {};
    users.forEach(function (u) { byId[u.id] = u; });

    var storeUrl = @json(route('admin.users.store'));
    var overlay = document.getElementById('user-overlay');
    var drawer = document.getElementById('user-drawer');
    var titleEl = document.getElementById('user-dr-title');
    var form = document.getElementById('user-form');
    var fMethod = document.getElementById('f-method');
    var fName = document.getElementById('f-name');
    var fEmail = document.getElementById('f-email');
    var fRole = document.getElementById('f-role');
    var fPass = document.getElementById('f-password');
    var fPassConf = document.getElementById('f-password-confirm');

    function openDrawer(mode, user) {
        if (!drawer || !overlay || !form) return;
        if (mode === 'edit' && user) {
            titleEl.textContent = 'Edit Pengguna';
            form.action = user.update_url;
            fMethod.value = 'PUT';
            fName.value = user.name || '';
            fEmail.value = user.email || '';
            fRole.value = user.role || 'pemohon';
            fPass.value = '';
            fPassConf.value = '';
            fPass.required = false;
            fPassConf.required = false;
            fPass.placeholder = 'Kosongkan jika tidak diubah';
            fPassConf.placeholder = 'Kosongkan jika tidak diubah';
        } else {
            titleEl.textContent = 'Tambah Pengguna';
            form.action = storeUrl;
            fMethod.value = 'POST';
            fName.value = '';
            fEmail.value = '';
            fRole.value = 'pemohon';
            fPass.value = '';
            fPassConf.value = '';
            fPass.required = true;
            fPassConf.required = true;
            fPass.placeholder = '';
            fPassConf.placeholder = '';
        }
        overlay.classList.add('is-open');
        drawer.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        drawer.setAttribute('aria-hidden', 'false');
        setTimeout(function () { if (fName) fName.focus(); }, 60);
    }

    function closeDrawer() {
        if (!drawer || !overlay) return;
        overlay.classList.remove('is-open');
        drawer.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        drawer.setAttribute('aria-hidden', 'true');
    }

    var addBtn = document.getElementById('btn-add-user');
    if (addBtn) {
        addBtn.addEventListener('click', function () { openDrawer('create', null); });
    }

    [].forEach.call(document.querySelectorAll('[data-edit-id]'), function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-edit-id');
            if (byId[id]) openDrawer('edit', byId[id]);
        });
    });

    var closeBtn = document.getElementById('user-dr-close');
    var cancelBtn = document.getElementById('user-dr-cancel');
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (cancelBtn) cancelBtn.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDrawer();
    });

    // Filter tabs & search
    var activeRole = 'all';
    var searchInput = document.getElementById('user-search');
    var rows = [].slice.call(document.querySelectorAll('#user-rows tr'));
    var emptyEl = document.getElementById('user-empty');

    function filterRows() {
        var q = searchInput ? (searchInput.value || '').toLowerCase().trim() : '';
        var visible = 0;
        rows.forEach(function (tr) {
            var roleMatch = activeRole === 'all' || tr.getAttribute('data-role') === activeRole;
            var textMatch = !q || (tr.getAttribute('data-search') || '').indexOf(q) !== -1;
            var show = roleMatch && textMatch;
            tr.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
    }

    [].forEach.call(document.querySelectorAll('[data-role-filter]'), function (tab) {
        tab.addEventListener('click', function () {
            activeRole = tab.getAttribute('data-role-filter') || 'all';
            [].forEach.call(document.querySelectorAll('[data-role-filter]'), function (t) {
                t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
            });
            filterRows();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', filterRows);
    }
})();
</script>
@endsection
