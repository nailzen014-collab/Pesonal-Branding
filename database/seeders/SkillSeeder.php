<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

/**
 * Skill yang tampil di halaman /skills (FR-08).
 */
class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'HTML', 'category' => 'frontend', 'level' => 90],
            ['name' => 'CSS', 'category' => 'frontend', 'level' => 85],
            ['name' => 'JavaScript', 'category' => 'frontend', 'level' => 80],
            ['name' => 'Tailwind CSS', 'category' => 'frontend', 'level' => 75],
            ['name' => 'PHP', 'category' => 'backend', 'level' => 85],
            ['name' => 'Laravel', 'category' => 'backend', 'level' => 80],
            ['name' => 'Blade', 'category' => 'backend', 'level' => 75],
            ['name' => 'MySQL', 'category' => 'database', 'level' => 75],
            ['name' => 'SQLite', 'category' => 'database', 'level' => 80],
            ['name' => 'Git', 'category' => 'tools', 'level' => 90],
            ['name' => 'GitHub', 'category' => 'tools', 'level' => 85],
            ['name' => 'Vite', 'category' => 'tools', 'level' => 70],
        ];

        foreach ($skills as $index => $skill) {
            Skill::updateOrCreate(
                ['name' => $skill['name']],
                [
                    'category' => $skill['category'],
                    'level' => $skill['level'],
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
