@extends('layouts.app')

@section('content')
<x-error-shell
    code="503"
    stroke-color="#F26A2D"
    title="Sedang Pemeliharaan"
    description="Sistem sedang dalam proses pemeliharaan berkala."
/>
@endsection
