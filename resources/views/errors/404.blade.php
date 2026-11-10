@extends('layouts.app')

@section('content')
<x-error-shell
    code="404"
    stroke-color="#ED1E28"
    title="Halaman Tidak Ditemukan"
    description="Halaman yang Anda tuju tidak tersedia atau telah dipindahkan."
/>
@endsection
