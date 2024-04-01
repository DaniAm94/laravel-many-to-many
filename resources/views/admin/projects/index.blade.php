@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <header class="d-flex justify-content-between align-items-center">
        <h1 class="text-center">Projects</h1>


        {{-- Filtro --}}
        <form action="{{ route('admin.projects.index') }}" method="GET">
            <div class="input-group">
                {{-- Filtro status --}}
                <select name="status_filter" class="form-select">
                    <option value="">Completati e non</option>
                    <option @if ($status_filter === 'completed') selected @endif value="completed">Completati</option>
                    <option @if ($status_filter === 'work in progress') selected @endif value="work in progress">In corso</option>
                </select>
                {{-- Filtro per tipo --}}
                <select name="type_filter" class="form-select">
                    <option value="">Tutti i tipi</option>
                    @foreach ($types as $type)
                        <option @if ($type_filter == $type->id) selected @endif value="{{ $type->id }}">
                            {{ $type->label }}</option>
                    @endforeach
                </select>
                {{-- Filtro per tipo --}}
                <select name="technology_filter" class="form-select">
                    <option value="">Tutte le tecnologie</option>
                    @foreach ($technologies as $technology)
                        <option @if ($technology_filter == $technology->id) selected @endif value="{{ $technology->id }}">
                            {{ $technology->label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-secondary" type="submit">Filtra</button>
            </div>
        </form>

    </header>
    <div class="border rounded mt-3">

        <table class="table table-striped table-hover rounded">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Titolo</th>
                    <th scope="col">Autore</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Tipologia</th>
                    <th scope="col">Tecnologie</th>
                    <th scope="col">Completato</th>
                    <th scope="col">Data creazione</th>
                    <th scope="col">Ultima modifica</th>
                    <th>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.projects.trash') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-trash me-2"></i>
                                Cestino</a>

                            <a href="{{ route('admin.projects.create') }}" class="btn btn-sm btn-success ">
                                <i class="fas fa-plus me-2"></i>Nuovo
                            </a>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr>
                        {{-- ID --}}
                        <th scope="row">{{ $project->id }}</th>

                        {{-- Titolo --}}
                        <td>{{ $project->title }}</td>

                        {{-- Autore --}}
                        <td>{{ $project->author ? $project->author->name : 'Anonimo' }}</td>

                        {{-- Slug --}}
                        <td>{{ $project->slug }}</td>

                        {{-- Tipologia --}}
                        <td><span class="badge "
                                @if ($project->type) style="background-color: {{ $project->type->color }}" @endif>{{ $project->type ? $project->type->label : 'Nessuna' }}</span>
                        </td>

                        {{-- Tecnologie --}}
                        <td>
                            @forelse ($project->technologies as $technology)
                                <span
                                    class="badge rounded-pill text-bg-{{ $technology->color }}">{{ $technology->label }}</span>
                            @empty
                            @endforelse
                        </td>

                        {{-- Stato --}}
                        <td>
                            <div class="form-check form-switch">
                                <form action="{{ route('admin.projects.toggle-status', $project->id) }}" method="POST"
                                    class="toggle-status" onclick="this.submit()">
                                    @csrf
                                    @method('PATCH')
                                    <input class="form-check-input" type="checkbox" role="button"
                                        id="toggle-status-btn-{{ $project->id }}"
                                        @if ($project->is_completed) checked @endif>
                                    <label class="form-check-label"
                                        for="toggle-status-btn-{{ $project->id }}">{{ $project->is_completed ? 'Completato' : 'In corso' }}</label>
                                </form>
                            </div>
                        </td>

                        {{-- Data creazione --}}
                        <td>{{ $project->getFormattedDate($project->created_at) }}</td>

                        {{-- Data ultima modifica --}}
                        <td>{{ $project->getFormattedDate($project->updated_at) }}</td>

                        {{-- Pulsanti --}}
                        <td>
                            <div class="d-flex justify-content-end gap-2 ">
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $project)
                                    <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                @endcan
                                @can('delete', $project)
                                    <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST"
                                        class="delete-form" data-title="{{ $project->title }}" data-bs-toggle="modal"
                                        data-bs-target="#delete-modal">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="far fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <h3>Non ci sono progetti al momento</h3>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    {{ $projects->links() }}
    {{-- Delete Modal --}}
    @include('includes.modal_confirmation_delete')
@endsection

@section('scripts')
    @vite('resources/js/delete_confirmation.js')
@endsection
