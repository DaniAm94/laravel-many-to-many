@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <header>
        {{-- Titolo --}}
        <h1 class="my-3 ">{{ $project->title }}</h1>
        {{-- Tipologia --}}
        <p>Tipologia: @if ($project->type)
                <span class="badge ms-2"
                    style="background-color: {{ $project->type->color }}">{{ $project->type->label }}</span>
            @else
                Nessuna
            @endif
        </p>
    </header>
    <hr>
    <div class="clearfix">
        @if ($project->image)
            <img style="width: 250px" src="{{ $project->printImage() }}" alt="{{ $project->title }}"
                class="me-3 float-start img-fluid ">
        @endif
        <p>{{ $project->description }}</p>
        <div>
            <strong>Data creazione: </strong> {{ $project->getFormattedDate($project->created_at) }}
            <strong class="ms-3">Ultima modifica: </strong> {{ $project->getFormattedDate($project->updated_at) }}
        </div>
    </div>
    <hr>
    <footer class="d-flex justify-content-between align--items-center">
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-secondary">
            <i class="fa-solid fa-rotate-left"></i>
            Torna indietro
        </a>
        <div class="d-flex justify-content-between gap-3">
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-pencil"></i>
                Modifica
            </a>
            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="delete-form"
                data-bs-toggle="modal" data-bs-target="#delete-modal">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="far fa-trash-can"></i>
                    Elimina</button>
            </form>
        </div>

    </footer>
    {{-- Delete Modal --}}
    @include('includes.modal_confirmation_delete')
@endsection

@section('scripts')
    @vite('resources/js/delete_confirmation.js')
@endsection
