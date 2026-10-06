<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Skill;

/**
 * Halaman statis: Tentang Saya, Skills, Sertifikat (FR-03, FR-04, FR-11).
 */
class PageController extends Controller
{
    public function tentang()
    {
        $experiences = Experience::ordered()->get();

        $grouped = $experiences->groupBy(fn (Experience $item) => $item->type)->all();

        return view('pages.tentang', [
            'educations' => $grouped['education'] ?? collect(),
            'works' => $grouped['experience'] ?? collect(),
            'organizations' => $grouped['organization'] ?? collect(),
        ]);
    }

    public function skills()
    {
        return view('pages.skills', [
            'groupedSkills' => Skill::groupedByCategory(),
        ]);
    }

    public function sertifikat()
    {
        return view('pages.sertifikat', [
            'certificates' => Certificate::ordered()->get(),
        ]);
    }
}
