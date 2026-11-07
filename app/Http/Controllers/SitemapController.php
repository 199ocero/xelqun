<?php

namespace App\Http\Controllers;

use App\Docs\DocsRepository;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(DocsRepository $docs): Response
    {
        $urls = [url('/')];

        foreach (array_keys($docs->packages()) as $package) {
            foreach (array_keys($docs->pages($package)) as $slug) {
                $urls[] = $docs->url($package, $slug);
            }
        }

        $entries = implode('', array_map(fn (string $url): string => '<url><loc>'.e($url).'</loc></url>', $urls));

        return response(
            '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$entries.'</urlset>',
            headers: ['Content-Type' => 'application/xml'],
        );
    }
}
