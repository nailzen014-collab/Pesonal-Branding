<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

/**
 * Daftar teknologi yang bisa dipilih pada form proyek (FR-12).
 */
class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            'HTML', 'CSS', 'JavaScript', 'PHP', 'Laravel',
            'Blade', 'Tailwind CSS', 'Alpine.js', 'Git', 'GitHub', 'Vite',
            'Vue.js', 'React', 'Node.js', 'Express', 'CodeIgniter',
            'MySQL', 'PostgreSQL', 'SQLite', 'Bootstrap', 'jQuery',
            'Chart.js', 'ApexCharts', 'Figma', 'REST API',
        ];

        foreach ($technologies as $index => $name) {
            Technology::updateOrCreate(
                ['name' => $name],
                ['sort_order' => $index + 1],
            );
        }
    }
}
