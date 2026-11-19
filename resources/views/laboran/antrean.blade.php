
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">Antrean Pengujian</h1>

    {{-- POP-UP NOTIFIKASI BERHASIL --}}
    @if (session('success'))
        <div id="success-popup"
            style="
                position: fixed;
                top: 25px;
                right: 25px;
                z-index: 9999;
                background-color: #dcfce7;
                color: #166534;
                border: 1px solid #86efac;
                border-left: 5px solid #16a34a;
                padding: 16px 22px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                min-width: 300px;
                max-width: 400px;
            ">

            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="
                    background-color: #16a34a;
                    color: white;
                    border-radius: 50%;
                    width: 25px;
                    height: 25px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: normal;
                ">✓</span>

                <div>
                    <p style="font-weight: 600; margin: 0;">
                        Berhasil!
                    </p>

                    <p style="font-size: 14px; margin: 3px 0 0 0;">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>

        <script>
            setTimeout(function () {
                const popup = document.getElementById('success-popup');

                if (popup) {
                    popup.style.transition = 'opacity 0.5s ease';
                    popup.style.opacity = '0';

                    setTimeout(function () {
                        popup.remove();
                    }, 500);
                }
            }, 3000);
        </script>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg border border-red-300">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @forelse ($tiketList as $tiket)

        <div class="border rounded-lg p-4 mb-4 shadow-sm">

            <h2 class="font-bold text-lg">
                {{ $tiket->nomor_tiket }}
            </h2>

            <p>Nama sampel: {{ $tiket->nama_sampel }}</p>

            <p>Status: {{ $tiket->status }}</p>

            {{-- TOMBOL TERIMA SAMPEL --}}
            @if ($tiket->status === 'Menunggu Verifikasi')

                <form method="POST"
                    action="{{ route('laboran.antrean.terima-fisik', $tiket->nomor_tiket) }}"
                    class="mt-3">

                    @csrf

                    <button type="submit"
                        style="background-color: #16a34a; color: white; padding: 9px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 400;">
                        Terima Sampel
                    </button>

                </form>

            {{-- TOMBOL PROSES MESIN --}}
            @elseif ($tiket->status === 'Dalam Antrean')

                <form method="POST"
                    action="{{ route('laboran.antrean.proses-mesin', $tiket->nomor_tiket) }}"
                    class="mt-3">

                    @csrf

                    <button type="submit"
                        style="background-color: #2563eb; color: white; padding: 9px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 400;">
                        Proses Mesin
                    </button>

                </form>

            @else

                <p class="text-gray-500 mt-2">
                    Tidak ada tindakan yang tersedia untuk status ini.
                </p>

            @endif

        </div>

    @empty

        <p>Belum ada tiket dalam antrean.</p>

    @endforelse

</div>
@endsection
