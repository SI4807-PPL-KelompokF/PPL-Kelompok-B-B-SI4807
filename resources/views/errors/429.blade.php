@extends('layouts.app')

@section('content')
<x-error-shell
    code="429"
    stroke-color="#F26A2D"
    title="Batas Permintaan Tercapai"
    description="Terlalu banyak permintaan dalam waktu singkat. Coba beberapa saat lagi."
/>
@endsection
