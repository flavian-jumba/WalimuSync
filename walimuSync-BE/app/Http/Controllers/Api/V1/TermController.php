<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TermResource;
use App\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TermController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $terms = Term::query()
            ->orderByDesc('is_active')
            ->orderByDesc('start_date')
            ->paginate();

        return TermResource::collection($terms);
    }

    public function show(Term $term): TermResource
    {
        return new TermResource($term);
    }

    public function active(): JsonResponse
    {
        $term = Term::query()->where('is_active', true)->first();

        if (! $term) {
            return response()->json(['message' => 'No active term found.'], 404);
        }

        return response()->json(['data' => new TermResource($term)]);
    }
}
