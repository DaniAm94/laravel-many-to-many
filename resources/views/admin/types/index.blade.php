@extends('layouts.app')

@section('title', 'Types')

@section('content')
    <header class="d-flex justify-content-between align-items-center">
        <h1 class="text-center">Types</h1>


        {{-- Filtro --}}
        {{-- <form action="{{ route('admin.projects.index') }}" method="GET">
            <div class="input-group">
                <select name="filter" class="form-select">
                    <option value="">Tutti</option>
                    <option @if ($filter === 'completed') selected @endif value="completed">Completati</option>
                    <option @if ($filter === 'work in progress') selected @endif value="work in progress">In corso</option>
                </select>
                <button class="btn btn-outline-secondary" type="submit">Filtra</button>
            </div>
        </form> --}}

    </header>
    <div class="border rounded mt-3">
        <table class="table table-striped table-hover rounded">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Etichetta</th>
                    <th scope="col">Colore</th>
                    <th scope="col">Progetti assegnati</th>
                    <th scope="col">Data creazione</th>
                    <th scope="col">Ultima modifica</th>
                    <th>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.projects.trash') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-trash me-2"></i>
                                Vedi cestino</a>

                            <a href="{{ route('admin.projects.create') }}" class="btn btn-sm btn-success ">
                                <i class="fas fa-plus me-2"></i>Nuovo
                            </a>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($types as $type)
                    <tr class="row-types">
                        {{-- ID --}}
                        <th scope="row">{{ $type->id }}</th>

                        {{-- Label --}}
                        <td>{{ $type->label }}</td>

                        {{-- Colore --}}
                        <td class="position-relative">
                            <div class="type-table-color" style="background-color: {{ $type->color }}"></div>
                        </td>

                        {{-- Progetti assegnati --}}
                        <td>
                            <ul>

                                @forelse ($type->projects as $project)
                                    <li class="mb-0">
                                        <a href="{{ route('admin.projects.show', $project->id) }}">
                                            {{ $project->title }}
                                        </a>
                                    </li>
                                @empty
                                    <li class="mb-0 text-secondary ">Nessun progetto</li>
                                @endforelse
                            </ul>
                        </td>

                        {{-- Data creazione --}}
                        <td>{{ $type->getFormattedDate($type->created_at) }}</td>

                        {{-- Data ultima modifica --}}
                        <td>{{ $type->getFormattedDate($type->updated_at) }}</td>

                        {{-- Pulsanti --}}
                        <td>
                            <div class="d-flex justify-content-end gap-2 ">
                                <a href="" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.types.edit', $type->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-pencil"></i>
                                </a>
                                <form action="{{ route('admin.types.destroy', $type->id) }}" method="POST"
                                    class="delete-form" data-title="{{ $type->label }}" data-bs-toggle="modal"
                                    data-bs-target="#delete-modal">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="far fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <h3>Non ci sono tipologie al momento</h3>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Delete Modal --}}
    @include('includes.modal_confirmation_delete')
@endsection

@section('scripts')
    @vite('resources/js/delete_confirmation.js')
@endsection
