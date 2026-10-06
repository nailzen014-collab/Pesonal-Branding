<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Technology;
use App\Services\GithubService;

/**
 * Halaman Beranda (FR-01, ringkasan statistik, proyek unggulan, CTA).
 */
class HomeController extends Controller
{
    public function index(GithubService $github)
    {
        $featuredProjects = Project::where('source', 'github')
            ->where('is_published', true)
            ->where('is_featured', true)
            ->with('technologies')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $stats = [
            'projects' => Project::published()->count(),
            'technologies' => Technology::count(),
            'skills' => Skill::count(),
            'repositories' => Project::where('source', 'github')->count(),
        ];

        $skills = Skill::ordered()->take(8)->get();
        $experiences = Experience::ordered()->take(4)->get();

        return view('pages.home', compact('featuredProjects', 'stats', 'skills', 'experiences'));
    }
}
