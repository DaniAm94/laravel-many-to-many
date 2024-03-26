@extends('layouts.app')

@section('title', 'Project')

@section('content')
    <div class="card my-5">
        <div class="card-header d-flex justify-content-between align-items-center ">
            @if ($project->type)
                <span class="badge" style="background-color: {{ $project->type->color }};">{{ $project->type->label }}</span>
            @else
                <span class="badge text-bg-secondary ">Nessuna categoria</span>
            @endif
            <a href="{{ route('guest.home') }}" class="btn btn-sm btn-primary ">Torna alla lista</a>
        </div>
        <div class="card-body">
            <div class="row">
                @if ($project->image)
                    <div class="col-3">
                        <img class="img-fluid" src="{{ $project->printImage() }}" alt="{{ $project->title }}">
                    </div>
                @endif
                <div class="col">
                    <h5 class="card-title mb-4 ">{{ $project->title }}</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">{{ $project->getFormattedDate('created_at') }}</h6>
                    <p class="card-text">{{ $project->description }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
