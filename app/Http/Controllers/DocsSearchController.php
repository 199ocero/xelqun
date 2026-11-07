<?php

namespace App\Http\Controllers;

use App\Docs\DocsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocsSearchController extends Controller
{
    public function __invoke(Request $request, DocsRepository $docs): JsonResponse
    {
        $query = $request->string('q')->limit(100, '')->trim()->toString();

        return response()->json(['results' => $query === ''
            ? $docs->suggestions($request->string('package')->toString())
            : $docs->search($query),
        ]);
    }
}
