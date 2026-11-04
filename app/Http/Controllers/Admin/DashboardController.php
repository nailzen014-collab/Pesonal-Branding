<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Technology;

/**
 * Dashboard admin: ringkasan konten (FR-21 sederhana).
 */
class DashboardController extends Controller
{
    public function index()
    {
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

        return view('admin.dashboard', compact('stats', 'recentMessages', 'recentProjects', 'recentPosts'));
    }
}
