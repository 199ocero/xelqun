<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-xq-panel data-xq-site>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Before paint, so a dark theme never flashes white. It's the same key the panels use. --}}
        <script>
            (function () {
                try {
                    var theme = localStorage.getItem('xq-panel-theme');
                    var dark = theme === 'dark' || (theme !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);
                } catch (error) {}
            })();
        </script>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <meta property="og:site_name" content="{{ config('app.name', 'Xelqun') }}">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Xelqun') }}</title>
        </x-inertia::head>

        @isset($page['props']['jsonLd'])
            <script type="application/ld+json">@json($page['props']['jsonLd'], JSON_UNESCAPED_SLASHES)</script>
        @endisset
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
