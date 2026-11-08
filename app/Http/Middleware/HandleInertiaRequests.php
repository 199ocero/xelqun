<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'links' => [
                'demo' => $request->getScheme().'://'.config('xelqun.demo_domain'),
                'github' => config('xelqun.github_url'),
                'githubStars' => Inertia::once(fn (): int|false => $this->githubStars()),
                'shareImage' => url('og.png'),
            ],
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }

    /**
     * The repository's star count, refreshed hourly in the background. False when GitHub can't be reached.
     */
    private function githubStars(): int|false
    {
        return Cache::flexible('github-stars', [3600, 86400], fn (): int|false => rescue(
            fn (): int => Http::timeout(3)
                ->acceptJson()
                ->get('https://api.github.com/repos/'.config('xelqun.github_repo'))
                ->throw()
                ->json('stargazers_count'),
            false,
            report: false,
        ));
    }
}
