<?php

namespace App\Http\Controllers;

use App\Demo\DemoAppearance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DemoAppearanceController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $choice = $request->validate([
            'primary' => ['nullable', Rule::in(DemoAppearance::primaryNames())],
            'gray' => ['required', Rule::in(DemoAppearance::grayNames())],
        ]);

        session()->put(['demo.primary' => $choice['primary'] ?? null, 'demo.gray' => $choice['gray']]);

        return back();
    }
}
