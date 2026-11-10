@extends('layouts.app')

@section('content')
<x-error-shell
    code="403"
    stroke-color="#F26A2D"
    title="Akses Ditolak"
    description="Akun Anda tidak memiliki izin untuk membuka halaman ini."
/>
@endsection
