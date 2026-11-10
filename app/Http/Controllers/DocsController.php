<?php

namespace App\Http\Controllers;

use App\Docs\DocsRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DocsController extends Controller
{
    public function home(DocsRepository $docs): Response
    {
        return Inertia::render('Home', [
            'packages' => collect($docs->packages())
                ->except(DocsRepository::INTRODUCTION)
                ->map(fn (array $package, string $key): array => [
                    'label' => $package['label'],
                    'tagline' => $package['tagline'],
                    'url' => "/{$key}",
                    'pages' => count($docs->pages($key)),
                ])
                ->values()
                ->all(),
            'canonical' => url('/'),
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@graph' => [
                    ['@type' => 'WebSite', 'name' => 'Xelqun', 'url' => url('/')],
                    [
                        '@type' => 'SoftwareSourceCode',
                        'name' => 'Xelqun',
                        'description' => 'Forms, tables and admin panels for Laravel, defined in PHP and rendered with Inertia and Vue.',
                        'url' => url('/'),
                        'codeRepository' => config('xelqun.github_url'),
                        'programmingLanguage' => ['PHP', 'Vue'],
                        'runtimePlatform' => 'Laravel',
                    ],
                ],
            ],
        ]);
    }

    public function show(DocsRepository $docs, string $package, ?string $page = null): Response|RedirectResponse
    {
        // A package's first page lives at its root, and search engines should only see one of them.
        if ($page === DocsRepository::OVERVIEW) {
            return redirect()->to($docs->url($package), 301);
        }

        $slug = $page ?? DocsRepository::OVERVIEW;
        $document = $docs->find($package, $slug);

        abort_if($document === null, 404);

        $definition = $docs->packages()[$package];
        $canonical = $docs->url($package, $slug);
        $group = $docs->group($package, $slug);

        return Inertia::render('docs/Show', [
            'document' => [
                'title' => $document['title'],
                'description' => $document['description'],
                'blocks' => $document['blocks'],
                'headings' => $document['headings'],
                'canonical' => $canonical,
                'group' => $group,
                ...$docs->neighbours($package, $slug),
            ],
            'docs' => [
                'package' => $package,
                'label' => $definition['label'],
                'url' => $docs->url($package),
                'sections' => $docs->sections($package, $slug),
            ],
            'jsonLd' => [
                '@context' => 'https://schema.org',
                '@type' => 'TechArticle',
                'headline' => $document['title'],
                'description' => $document['description'],
                'url' => $canonical,
                'isPartOf' => ['@type' => 'WebSite', 'name' => 'Xelqun', 'url' => url('/')],
                'breadcrumb' => [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => $definition['label'], 'item' => $docs->url($package)],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => $document['title'], 'item' => $canonical],
                    ],
                ],
            ],
        ]);
    }
}
