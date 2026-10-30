@extends('layouts.app')

@section('body_class', 'is-auth')

@section('content')
<x-auth-shell
    :heading="trim($__env->yieldContent('heading'))"
    :subheading="trim($__env->yieldContent('subheading'))"
    :aside-text="trim($__env->yieldContent('aside_text', 'Satu akun untuk mengajukan, melacak, dan mengunduh hasil pengujian laboratorium Telkom University.'))"
>
    @yield('form')

    @hasSection('foot')
        <x-slot name="foot">
            @yield('foot')
        </x-slot>
    @endif
</x-auth-shell>
@endsection

