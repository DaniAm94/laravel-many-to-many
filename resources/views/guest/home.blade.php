@extends('layouts.app')

@section('title', 'Home')

@section('content')

    <header>
        <h1>BoolFolio</h1>
        <h3>I progetti completati</h3>
        {{-- Pagination --}}
        {{ $projects->links() }}
    </header>
    <hr>
    <div class="row">

        @forelse ($projects as $project)
            <div class="col-4">

                <div class="card my-5">
                    <div class="card-header d-flex justify-content-between align-items-center ">
                        {{-- Type --}}
                        @if ($project->type)
                            <span class="badge"
                                style="background-color: {{ $project->type->color }};">{{ $project->type->label }}</span>
                        @else
                            <span class="badge text-bg-secondary ">Nessuna categoria</span>
                        @endif

                        {{-- Pulsante vedi dettaglio --}}
                        <a href="{{ route('guest.projects.show', $project->slug) }}" class="btn btn-sm btn-primary ">Vedi</a>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            {{-- Immagine --}}
                            @if ($project->image)
                                <div class="col-3">
                                    <img class="img-fluid" src="{{ $project->printImage() }}" alt="{{ $project->title }}">
                                </div>
                            @endif
                            <div class="col">

                                {{-- Titolo --}}
                                <h5 class="card-title mb-2 text-break">{{ $project->title }}</h5>

                                {{-- Data creazione --}}
                                <small class="card-subtitle text-body-secondary">
                                    {{ $project->getFormattedDate('created_at') }}
                                </small>

                                {{-- Descrizione --}}
                                <p class="card-text mt-2">{{ $project->getAbstract(70) }}</p>

                                {{-- Tecnologie --}}
                                @forelse ($project->technologies as $technology)
                                    <span
                                        class="badge rounded-pill text-bg-{{ $technology->color }}">{{ $technology->label }}</span>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <h3 class="text-center">Non ci sono progetti</h3>
            </div>
        @endforelse
    </div>
    {{-- Pagination --}}
    {{ $projects->links() }}
@endsection
