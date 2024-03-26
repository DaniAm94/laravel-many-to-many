<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        // Nella home sono visibili solo i progetti completati
        $projects = Project::whereIsCompleted(true)->orderByDesc('created_at')->paginate(6);
        return view('guest.home', compact('projects'));
    }
}
