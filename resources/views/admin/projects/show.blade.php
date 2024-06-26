@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <header>
        {{-- Titolo --}}
        <h1 class="my-3 ">{{ $project->title }}</h1>

        {{-- Autore --}}
        <h5>{{ $project->author ? $project->author->name : 'Anonimo' }}</h5>

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
        {{-- Immagine --}}
        @if ($project->image)
            <img style="width: 250px" src="{{ $project->printImage() }}" alt="{{ $project->title }}"
                class="me-3 float-start img-fluid ">
        @endif

        {{-- Descrizione --}}
        <p>{{ $project->description }}</p>

        <div class="d-flex flex-column row-gap-3  ">
            {{-- Creazione e modifica --}}
            <div class="dates-info">
                <strong>Data creazione: </strong> {{ $project->getFormattedDate($project->created_at) }}
                <strong class="ms-3">Ultima modifica: </strong> {{ $project->getFormattedDate($project->updated_at) }}
            </div>

            {{-- Tecnologie --}}
            <div class="technologies">
                @forelse ($project->technologies as $technology)
                    <span class="badge rounded-pill text-bg-{{ $technology->color }}">{{ $technology->label }}</span>
                @empty
                @endforelse
            </div>
        </div>
    </div>
    <hr>
    {{-- Barra pulsanti --}}
    <footer class="d-flex justify-content-between align--items-center">

        {{-- Indietro --}}
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-secondary">
            <i class="fa-solid fa-rotate-left"></i>
            Torna indietro
        </a>

        <div class="d-flex justify-content-between gap-3">

            {{-- Modifica --}}
            @can('update', $project)
                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-pencil"></i>
                    Modifica
                </a>
            @endcan

            {{-- Elimina --}}
            @can('delete', $project)
                <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="delete-form"
                    data-bs-toggle="modal" data-bs-target="#delete-modal">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="far fa-trash-can"></i>
                        Elimina</button>
                </form>
            @endcan
        </div>

    </footer>
    {{-- Delete Modal --}}
    @include('includes.modal_confirmation_delete')
@endsection

@section('scripts')
    @vite('resources/js/delete_confirmation.js')
@endsection
