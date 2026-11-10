<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Xelqun\Panels\Support\Colors\Color;

/**
 * The demo logo in the visitor's primary color. An <img> can't read the theme's
 * CSS variables, so the color comes in the URL and each color caches on its own.
 */
class DemoLogoController extends Controller
{
    public function __invoke(string $theme, string $color): Response
    {
        $solid = Color::solidFor("#{$color}");
        $text = $theme === 'dark' ? '#f4f4f5' : '#1c1c1e';

        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118 32" fill="none"><rect width="32" height="32" rx="8" fill="{$solid['fill']}"/><path d="M10 10.5 22 21.5M22 10.5 10 21.5" stroke="{$solid['text']}" stroke-width="3" stroke-linecap="round"/><circle cx="22.5" cy="10" r="2.25" fill="{$solid['text']}"/><text x="42" y="22.5" font-family="Inter, system-ui, sans-serif" font-size="19" font-weight="600" fill="{$text}">Xelqun</text></svg>
            SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
