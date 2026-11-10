@extends('layouts.app')

@section('content')
<x-error-shell
    code="500"
    stroke-color="#ED1E28"
    title="Gangguan Server"
    description="Terjadi kendala teknis pada sistem. Silakan coba kembali nanti."
/>
@endsection
