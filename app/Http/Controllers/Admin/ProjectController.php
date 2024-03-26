<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Prendo il filtro di ricerca dalla request
        $filter = $request->query('filter');
        // Preparo la query dei progetti ordinata per data di modifica e di creazione
        $query = Project::orderByDesc('updated_at')->orderByDesc('created_at');
        if ($filter) {
            $value = $filter === 'completed';
            $query->whereIsCompleted($value);
        }
        // pagino 10 progetti alla volta
        $projects = $query->paginate(10)->withQueryString();
        return view('admin.projects.index', compact('projects', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Type::select('id', 'label')->get();
        $project = new Project();
        return view('admin.projects.create', compact('project', 'types'));
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
        $project->save();
        return to_route('admin.projects.show', $project->id);
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
        $types = Type::select('id', 'label')->get();
        return view('admin.projects.edit', compact('project', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
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
        $project->fill($data);
        $project->save();
        return to_route('admin.projects.show', $project->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
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
        $project->restore();
        return to_route('admin.projects.index')->with('type', 'success')->with('message', 'Progetto ripristinato');
    }
    public function drop(Project $project)
    {
        if ($project->image) Storage::delete($project->image);
        $project->forceDelete();

        return to_route('admin.projects.trash')->with('type', 'warning')->with('message', 'Progetto eliminato definitivamente');
    }
    public function massiveDrop()
    {
        $projects = Project::onlyTrashed()->get();
        foreach ($projects as $project) {
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
