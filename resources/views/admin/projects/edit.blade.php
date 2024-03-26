@extends('layouts.app')
@section('title', 'Edit')
@section('content')
    <header>
        <h1 class="text-center">Inserisci un nuovo progetto</h1>
    </header>
    <hr>

    {{-- Form --}}
    @include('includes.projects.form')
@endsection

@section('scripts')
    {{-- Image Preview --}}
    @vite('resources/js/preview.js')
    {{-- Slug --}}
    @vite('resources/js/slug.js')
@endsection
