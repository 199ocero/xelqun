<?php

namespace App\Http\Controllers;

use App\Demo\DemoAppearance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Xelqun\Panels\Support\Preset;

class DemoAppearanceController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $choice = $request->validate([
            'primary' => ['nullable', Rule::in(DemoAppearance::primaryNames())],
            'gray' => ['required', Rule::in(DemoAppearance::grayNames())],
            'preset' => ['nullable', Rule::enum(Preset::class)],
        ]);

        session()->put(['demo.primary' => $choice['primary'] ?? null, 'demo.gray' => $choice['gray'], 'demo.preset' => $choice['preset'] ?? null]);

        return back();
    }
}
