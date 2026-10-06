<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * CRUD proyek di panel admin (FR-16).
 *
 * Konsep: route model binding membuat $project otomatis diambil dari URL,
 * sehingga controller tidak perlu menulis query manual.
 */
class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->value();

        $projects = Project::with('technologies')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.projects.index', compact('projects', 'search'));
    }

    public function create(): View
    {
        return view('admin.projects.form', [
            'project' => new Project(['source' => 'manual', 'is_published' => true, 'is_featured' => false]),
            'technologies' => Technology::orderBy('name')->get(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('technologies');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('project-thumbnails', 'public');
        }

        $project = Project::create($data);
        $project->technologies()->sync($request->input('technologies', []));

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyek "'.$project->title.'" berhasil ditambahkan.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', [
            'project' => $project,
            'technologies' => Technology::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $request->safe()->except('technologies');

        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($project->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('project-thumbnails', 'public');
        }

        $project->update($data);
        $project->technologies()->sync($request->input('technologies', []));

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyek "'.$project->title.'" berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->deleteFile($project->thumbnail);
        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }

    public function toggleFeatured(Project $project): RedirectResponse
    {
        $project->update([
            'is_featured' => ! $project->is_featured,
        ]);

        $label = $project->is_featured ? 'ditandai sebagai unggulan' : 'dihapus dari unggulan';

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyek "'.$project->title.'" '.$label.'.');
    }

    /**
     * Hapus file lama agar folder storage tidak penuh file tidak terpakai.
     */
    private function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
