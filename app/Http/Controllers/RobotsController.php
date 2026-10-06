<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * robots.txt: memberi tahu crawler mana yang boleh diindeks.
 */
class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /login',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode(PHP_EOL, $lines).PHP_EOL, 200, ['Content-Type' => 'text/plain']);
    }
}
