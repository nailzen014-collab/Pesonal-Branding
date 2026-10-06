<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Halaman publik portofolio: daftar proyek (FR-05) dan detail proyek (FR-06).
 */
class ProjectController extends Controller
{
    /** Jumlah proyek yang dikirim per satu halaman. */
    private const PER_PAGE = 9;

    /**
     * Daftar proyek + filter kategori/teknologi + pencarian judul (FR-07).
     *
     * Parameter `partial=1` dipakai tombol "muat lebih banyak": halaman
     * penuh (diminta tanpa JavaScript) dan potongan HTML untuk
     * *append* memakai query yang sama, jadi tidak ada duplikasi logika.
     */
    public function index(Request $request)
    {
        [$search, $category, $technology] = $this->filters($request);

        $projects = $this->query($search, $category, $technology)
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Opsi filter diambil dari data yang sudah dipakai proyek.
        $categories = Project::published()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $technologies = Technology::usedInProjects()->orderBy('name')->pluck('name');

        if ($request->boolean('partial')) {
            return response()
                ->view('pages.portofolio.partials.cards', [
                    'projects' => $projects,
                    'nextPage' => $projects->hasMorePages() ? $projects->currentPage() + 1 : null,
                    'query' => $request->only(['q', 'category', 'technology']),
                ])
                ->header('X-Next-Page', (string) ($projects->hasMorePages() ? $projects->currentPage() + 1 : ''));
        }

        return view('pages.portofolio.index', compact(
            'projects',
            'search',
            'category',
            'technology',
            'categories',
            'technologies',
        ));
    }

    /**
     * Detail proyek. Route memakai slug supaya ramah SEO (NFR-04).
     */
    public function show(string $slug)
    {
        $project = Project::published()
            ->with('technologies')
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Project::published()
            ->with('technologies')
            ->whereKeyNot($project->id)
            ->when($project->category, fn ($query) => $query->where('category', $project->category))
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('pages.portofolio.show', compact('project', 'related'));
    }

    /**
     * Tiga nilai filter dari request, sudah dibersihkan dari spasi.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    private function filters(Request $request): array
    {
        return [
            $request->string('q')->trim()->value(),
            $request->string('category')->trim()->value(),
            $request->string('technology')->trim()->value(),
        ];
    }

    /**
     * Query dasar daftar proyek.
     *
     * @return Builder<Project>
     */
    private function query(string $search, string $category, string $technology)
    {
        return Project::published()
            ->with('technologies')
            ->filter($search ?: null, $category ?: null, $technology ?: null)
            ->orderBy('sort_order')
            ->orderByDesc('created_at');
    }
}
