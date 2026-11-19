<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Technology;
use Illuminate\Contracts\View\View;

/**
 * Dashboard admin: ringkasan konten (FR-21 sederhana).
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $skillCategoryStats = Skill::query()
            ->select('category')
            ->selectRaw('COUNT(*) as skill_count, AVG(level) as average_level')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        $skillChart = collect(Skill::CATEGORIES)
            ->map(fn (string $label, string $category): array => [
                'category' => $category,
                'label' => $label,
                'count' => (int) ($skillCategoryStats->get($category)?->skill_count ?? 0),
                'average_level' => (float) ($skillCategoryStats->get($category)?->average_level ?? 0),
            ])
            ->values();

        $stats = [
            'projects' => Project::count(),
            'published_projects' => Project::published()->count(),
            'featured_projects' => Project::featured()->count(),
            'posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'skills' => Skill::count(),
            'technologies' => Technology::count(),
            'unread_messages' => Message::unread()->count(),
        ];

        $recentMessages = Message::latest()->take(5)->get();
        $recentProjects = Project::with('technologies')->latest()->take(5)->get();
        $recentPosts = Post::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'skillChart', 'recentMessages', 'recentProjects', 'recentPosts'));
    }
}
