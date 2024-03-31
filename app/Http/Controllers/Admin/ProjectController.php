<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Technology;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Prendo i filtri di ricerca dalla request
        $status_filter = $request->query('status_filter');
        $type_filter = $request->query('type_filter');
        $technology_filter = $request->query('technology_filter');



        // Faccio la query dei progetti ordinata per data di modifica e di creazione e applico i vari filtri
        $projects = Project::statusFilter($status_filter)
            ->typeFilter($type_filter)
            ->technologyFilter($technology_filter)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $technologies = Technology::select('id', 'label')->get();
        $types = Type::select('id', 'label')->get();
        return view('admin.projects.index', compact('projects', 'types', 'technologies', 'status_filter', 'type_filter', 'technology_filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Type::select('id', 'label')->get();
        $technologies = Technology::select('id', 'label')->get();
        $project = new Project();
        return view('admin.projects.create', compact('project', 'types', 'technologies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        $data['is_completed'] = Arr::exists($data, 'is_completed');
        $project = new Project();
        $project->fill($data);
        if (Arr::exists($data, 'image')) {
            $extension = $data['image']->extension(); // restituisce l'estensione del file senza punto
            $img_url = Storage::putFileAs('project_images', $data['image'], "$project->slug.$extension");
            $project->image = $img_url;
        }
        $project->user_id = Auth::id();
        $project->save();
        if (Arr::exists($data, 'technologies')) {
            $project->technologies()->attach($data['technologies']);
        }
        return to_route('admin.projects.show', $project->id)->with('message', 'Progetto creato con successo')->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {

        // if ($project->user_id !== Auth::id()) {
        //     return to_route('admin.projects.index')->with('type', 'warning')->with('message', 'Non sei autorizzato a modificare questo progetto');
        // }
        // Autorizzazione tramiite ProjectPolicy + Gate
        Gate::authorize('update-project', $project);

        $types = Type::select('id', 'label')->get();
        $technologies = Technology::select('id', 'label')->get();
        $prev_technologies = $project->technologies->pluck('id')->toArray();
        return view('admin.projects.edit', compact('project', 'types', 'technologies', 'prev_technologies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {

        // Autorizzazione tramite ProjectPolicy
        // $this->authorize('update', $project);

        // Autorizzazione tramiite ProjectPolicy + Gate
        Gate::authorize('update-project', $project);


        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        $data['is_completed'] = Arr::exists($data, 'is_completed');
        // Controllo se mi arriva un file
        if (Arr::exists($data, 'image')) {
            // Controllo se c'era già un'immagine e la cancello
            if ($project->image) Storage::delete($project->image);
            $extension = $data['image']->extension(); // restituisce l'estensione del file senza punto

            // Lo salvo e prendo l'url
            $img_url = Storage::putFileAs('project_images', $data['image'], "{$data['slug']}.$extension");

            $project->image = $img_url;
        }
        $project->update($data);

        if (Arr::exists($data, 'technologies')) {
            $project->technologies()->sync($data['technologies']);
        } elseif (!Arr::exists($data, 'technologies') && $project->has('technologies')) {
            $project->technologies()->detach();
        }
        return to_route('admin.projects.show', $project->id)->with('message', 'Progetto modificato con successo')->with('type', 'warning');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Autorizzazione tramite gate + project policy
        Gate::authorize('destroy-project', $project);
        $project->delete();
        return to_route('admin.projects.index')
            ->with('toast-button-type', 'danger')
            ->with('toast-message', 'Progetto eliminato')
            ->with('toast-label', config('app.name'))
            ->with('toast-method', 'PATCH')
            ->with('toast-route', route('admin.projects.restore', $project->id))
            ->with('toast-button-label', 'Annulla');
    }

    // Rotte Soft Delete
    public function trash()
    {
        $projects = Project::onlyTrashed()->paginate(10);
        return view('admin.projects.trash', compact('projects'));
    }
    public function restore(Project $project)
    {
        Gate::authorize('restore-project', $project);
        $project->restore();
        return to_route('admin.projects.index')->with('type', 'success')->with('message', 'Progetto ripristinato');
    }
    public function drop(Project $project)
    {
        Gate::authorize('drop-project', $project);
        if ($project->has('technologies')) $project->technologies()->detach();
        if ($project->image) Storage::delete($project->image);
        $project->forceDelete();

        return to_route('admin.projects.trash')->with('type', 'warning')->with('message', 'Progetto eliminato definitivamente');
    }
    public function massiveDrop()
    {
        $projects = Project::onlyTrashed()->get();
        foreach ($projects as $project) {
            if ($project->has('technologies')) $project->technologies()->detach();
            if ($project->image) Storage::delete($project->image);
            $project->forceDelete();
        }

        return to_route('admin.projects.trash')->with('type', 'warning')->with('message', 'Progetti eliminati definitivamente');
    }

    public function toggleStatus(Project $project)
    {
        $project->is_completed = !$project->is_completed;
        $project->save();
        $action = $project->is_completed ? '"completato"' : '"in corso"';
        $type = $project->is_completed ? 'success' : 'info';
        return back()->with('message', "Lo status del progetto \"$project->title\" è stato cambiato in $action")->with('type', "$type");
    }
}
