<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Technology;

/**
 * Halaman Beranda (FR-01, ringkasan statistik, proyek unggulan, CTA).
 */
class HomeController extends Controller
{
    public function index()
    {
        // with('technologies') = eager loading, mencegah query berulang (NFR-01).
        $featuredProjects = Project::published()
            ->featured()
            ->with('technologies')
            ->orderBy('sort_order')
            ->take(3)
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
