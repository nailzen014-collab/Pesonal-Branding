<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExperienceRequest;
use App\Http\Requests\UpdateExperienceRequest;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CRUD timeline pendidikan / pengalaman / organisasi (FR-18).
 */
class ExperienceController extends Controller
{
    public function index(): View
    {
        $experiences = Experience::ordered()->paginate(15);

        return view('admin.experiences.index', compact('experiences'));
    }

    public function create(): View
    {
        return view('admin.experiences.form', ['experience' => new Experience(['type' => 'education'])]);
    }

    public function store(StoreExperienceRequest $request): RedirectResponse
    {
        $experience = Experience::create($request->validated());

        return redirect()
            ->route('admin.experiences.index')
            ->with('success', 'Timeline "'.$experience->title.'" berhasil ditambahkan.');
    }

    public function edit(Experience $experience): View
    {
        return view('admin.experiences.form', compact('experience'));
    }

    public function update(UpdateExperienceRequest $request, Experience $experience): RedirectResponse
    {
        $experience->update($request->validated());

        return redirect()
            ->route('admin.experiences.index')
            ->with('success', 'Timeline berhasil diperbarui.');
    }

    public function destroy(Experience $experience): RedirectResponse
    {
        $experience->delete();

        return redirect()
            ->route('admin.experiences.index')
            ->with('success', 'Timeline berhasil dihapus.');
    }
}
