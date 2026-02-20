<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFeeCollectionRequest;
use App\Http\Requests\Api\V1\UpdateFeeCollectionRequest;
use App\Http\Resources\Api\V1\FeeCollectionResource;
use App\Http\Resources\Api\V1\FeePaymentResource;
use App\Models\FeeCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeeCollectionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $collections = FeeCollection::query()
            ->with(['schoolClass', 'term', 'assignedTeacher'])
            ->withCount('payments')
            ->withSum('payments', 'amount_paid')
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->query('term_id'), fn ($q, $termId) => $q->where('term_id', $termId))
            ->when($request->query('school_class_id'), fn ($q, $classId) => $q->where('school_class_id', $classId))
            ->orderByDesc('created_at')
            ->paginate();

        return FeeCollectionResource::collection($collections);
    }

    public function show(FeeCollection $feeCollection): FeeCollectionResource
    {
        $feeCollection->load(['schoolClass', 'term', 'assignedTeacher'])
            ->loadCount('payments')
            ->loadSum('payments', 'amount_paid');

        return new FeeCollectionResource($feeCollection);
    }

    public function store(StoreFeeCollectionRequest $request): JsonResponse
    {
        $collection = FeeCollection::create($request->validated());
        $collection->load(['schoolClass', 'term', 'assignedTeacher']);

        return response()->json([
            'message' => 'Fee collection created.',
            'data' => new FeeCollectionResource($collection),
        ], 201);
    }

    public function update(UpdateFeeCollectionRequest $request, FeeCollection $feeCollection): JsonResponse
    {
        $feeCollection->update($request->validated());
        $feeCollection->load(['schoolClass', 'term', 'assignedTeacher']);

        return response()->json([
            'message' => 'Fee collection updated.',
            'data' => new FeeCollectionResource($feeCollection),
        ]);
    }

    public function destroy(FeeCollection $feeCollection): JsonResponse
    {
        $feeCollection->delete();

        return response()->json(['message' => 'Fee collection deleted.']);
    }

    public function payments(FeeCollection $feeCollection): AnonymousResourceCollection
    {
        $payments = $feeCollection->payments()
            ->with(['student', 'collector'])
            ->orderByDesc('payment_date')
            ->paginate();

        return FeePaymentResource::collection($payments);
    }

    public function myCollections(Request $request): AnonymousResourceCollection
    {
        $collections = FeeCollection::query()
            ->with(['schoolClass', 'term'])
            ->withCount('payments')
            ->withSum('payments', 'amount_paid')
            ->where('assigned_teacher_id', $request->user()->id)
            ->where('status', 'open')
            ->orderByDesc('created_at')
            ->paginate();

        return FeeCollectionResource::collection($collections);
    }
}
