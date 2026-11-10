<?php

use App\Http\Controllers\DemoAppearanceController;
use App\Http\Controllers\DemoInboxController;
use App\Http\Controllers\DemoLogoController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\DocsSearchController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::domain(config('xelqun.demo_domain'))->group(function (): void {
    Route::get('inbox', DemoInboxController::class)->name('demo.inbox');
    Route::post('_demo/appearance', DemoAppearanceController::class)->middleware('throttle:60,1')->name('demo.appearance');
    Route::get('_demo/logo/{theme}/{color}.svg', DemoLogoController::class)->whereIn('theme', ['light', 'dark'])->where('color', '[0-9a-f]{6}')->name('demo.logo');
});

// Both hosts: the docs embed these, and so does the Components page of the demo.
Route::controller(ExampleController::class)->prefix('examples')->middleware('throttle:120,1')->group(function (): void {
    Route::get('{example}', 'show')->name('examples.show');
    Route::post('{example}', 'submit')->name('examples.submit');
});

Route::domain(config('xelqun.site_domain'))->group(function (): void {
    Route::get('/', [DocsController::class, 'home'])->name('home');

    Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
    Route::get('search', DocsSearchController::class)->middleware('throttle:60,1')->name('docs.search');

    Route::get('{package}/{page?}', [DocsController::class, 'show'])
        ->whereIn('package', array_keys(config('docs.packages')))
        ->where('page', '.*')
        ->name('docs.show');
});
