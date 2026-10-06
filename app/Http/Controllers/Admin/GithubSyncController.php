<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportGithubReposRequest;
use App\Models\Project;
use App\Services\GithubService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Sinkronisasi repository GitHub (FR-14).
 *
 * Alur: admin memilih repo dari daftar hasil API, lalu repo tersebut
 * disimpan sebagai proyek dengan status draft, supaya admin bisa menulis
 * deskripsi sendiri sebelum menampilkannya ke pengunjung.
 */
class GithubSyncController extends Controller
{
    public function __construct(private readonly GithubService $github) {}

    public function index(): View
    {
        $repositories = $this->github->repositories(sort: 'stars');

        // Repo yang sudah ada di database ditandai agar tidak dobel.
        $importedIds = Project::whereNotNull('github_id')->pluck('github_id')->all();

        return view('admin.github.index', [
            'repositories' => $repositories,
            'importedIds' => $importedIds,
            'apiAvailable' => $repositories !== [],
        ]);
    }

    public function store(ImportGithubReposRequest $request): RedirectResponse
    {
        $selected = collect($request->input('repos', []))->map(fn ($id) => (int) $id);

        $repositories = collect($this->github->repositories())
            ->filter(fn (array $repo) => $selected->contains($repo['id']));

        if ($repositories->isEmpty()) {
            return back()->with('error', 'Tidak ada repository yang dipilih.');
        }

        $publish = $request->boolean('publish');

        foreach ($repositories as $repo) {
            Project::updateOrCreate(
                ['github_id' => $repo['id']],
                [
                    'title' => Str::headline($repo['name']),
                    'summary' => Str::limit($repo['description'], 200, ''),
                    'category' => $repo['language'],
                    'repo_url' => $repo['url'],
                    'source' => 'github',
                    'is_published' => $publish,
                ],
            );
        }

        return redirect()
            ->route('admin.github.index')
            ->with('success', $repositories->count().' repository berhasil diimpor.');
    }

    public function refresh(): RedirectResponse
    {
        $this->github->repositories(fresh: true);

        return back()->with('success', 'Daftar repository diperbarui dari GitHub.');
    }
}
