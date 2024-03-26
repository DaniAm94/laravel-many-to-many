@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <header class="d-flex justify-content-between align-items-center">
        <h1 class="text-center">Progetti eliminati</h1>

        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-success ">Vedi i progetti attivi</a>

    </header>
    <table class="table table-info table-striped table-hover mt-3 rounded">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Titolo</th>
                <th scope="col">Slug</th>
                <th scope="col">Stato</th>
                <th scope="col">Data creazione</th>
                <th scope="col">Ultima modifica</th>
                <th>
                    <div class="d-flex justify-content-end ">

                        {{-- # TODO --}}

                        <form action="{{ route('admin.projects.massive-drop') }}" method="POST" class="delete-form"
                            data-title="" data-bs-toggle="modal" data-bs-target="#delete-modal">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash me-2 "></i>Svuota cestino
                            </button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($projects as $project)
                <tr>
                    <th scope="row">{{ $project->id }}</th>
                    <td>{{ $project->title }}</td>
                    <td>{{ $project->slug }}</td>
                    <td>{{ $project->is_completed ? 'Completato' : 'In corso' }}</td>
                    <td>{{ $project->getFormattedDate($project->created_at) }}</td>
                    <td>{{ $project->getFormattedDate($project->updated_at) }}</td>
                    <td>
                        <div class="d-flex justify-content-end gap-2 ">
                            <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-pencil"></i>
                            </a>
                            <form action="{{ route('admin.projects.drop', $project->id) }}" method="POST"
                                class="delete-form" data-title="{{ $project->title }}" data-bs-toggle="modal"
                                data-bs-target="#delete-modal">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="far fa-trash-can"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.projects.restore', $project->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-arrows-rotate"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <h3>Non ci sono progetti al momento</h3>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{-- Pagination --}}
    {{ $projects->links() }}
    {{-- Delete Modal --}}
    @include('includes.modal_confirmation_delete')
@endsection

@section('scripts')
    @vite('resources/js/delete_confirmation.js')
@endsection
