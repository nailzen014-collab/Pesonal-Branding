<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

/**
 * sitemap.xml untuk mesin pencari (NFR-04).
 *
 * Konsep: sitemap adalah daftar URL halaman website. Mempermudah Google
 * menemukan halaman baru tanpa harus menunggu website di-crawl.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $staticUrls = [
            ['loc' => route('home'), 'priority' => '1.0', 'freq' => 'weekly'],
            ['loc' => route('tentang'), 'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => route('skills'), 'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => route('portofolio'), 'priority' => '0.9', 'freq' => 'weekly'],
            ['loc' => route('sertifikat'), 'priority' => '0.6', 'freq' => 'monthly'],
            ['loc' => route('kontak'), 'priority' => '0.7', 'freq' => 'yearly'],
        ];

        $projectUrls = Project::published()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Project $project) => [
                'loc' => route('portofolio.show', $project->slug),
                'priority' => $project->is_featured ? '0.9' : '0.7',
                'freq' => 'monthly',
                'lastmod' => $project->updated_at?->toAtomString(),
            ])
            ->all();

        $urls = array_merge($staticUrls, $projectUrls);

        $xml = view('seo.sitemap', compact('urls'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
