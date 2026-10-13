<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GithubWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $secret = config('services.github.webhook_secret');

        if ($secret && ! $this->verifySignature($request, $secret)) {
            Log::warning('GitHub webhook: tanda tangan tidak valid');
            return response('Invalid signature', 401);
        }

        $event = $request->header('X-GitHub-Event');
        $payload = $request->json()->all();

        if ($event === 'repository' && ($payload['action'] ?? '') === 'created') {
            $repo = $payload['repository'] ?? null;

            if ($repo && ! $repo['private']) {
                Log::info('GitHub webhook: repository baru dibuat', [
                    'repo' => $repo['full_name'],
                    'id' => $repo['id'],
                ]);

                Artisan::queue('portfolio:import-github', [
                    '--publish' => true,
                    '--include-forks' => true,
                ]);
            }
        }

        return response('OK', 200);
    }

    private function verifySignature(Request $request, string $secret): bool
    {
        $signature = $request->header('X-Hub-Signature-256');

        if (! $signature) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}