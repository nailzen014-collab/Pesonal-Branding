<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CRUD skill (FR-17).
 */
class SkillController extends Controller
{
    public function index(): View
    {
        $skills = Skill::ordered()->paginate(15);

        return view('admin.skills.index', compact('skills'));
    }

    public function create(): View
    {
        return view('admin.skills.form', ['skill' => new Skill(['level' => 75])]);
    }

    public function store(StoreSkillRequest $request): RedirectResponse
    {
        $skill = Skill::create($request->validated());

        return redirect()
            ->route('admin.skills.index')
            ->with('success', 'Skill "'.$skill->name.'" berhasil ditambahkan.');
    }

    public function edit(Skill $skill): View
    {
        return view('admin.skills.form', compact('skill'));
    }

    public function update(UpdateSkillRequest $request, Skill $skill): RedirectResponse
    {
        $skill->update($request->validated());

        return redirect()
            ->route('admin.skills.index')
            ->with('success', 'Skill "'.$skill->name.'" berhasil diperbarui.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $skill->delete();

        return redirect()
            ->route('admin.skills.index')
            ->with('success', 'Skill berhasil dihapus.');
    }
}
