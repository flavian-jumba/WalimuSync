<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFeePaymentRequest;
use App\Http\Resources\Api\V1\FeePaymentResource;
use App\Models\FeePayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeePaymentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $payments = FeePayment::query()
            ->with(['feeCollection', 'student', 'collector'])
            ->when($request->query('fee_collection_id'), fn ($q, $id) => $q->where('fee_collection_id', $id))
            ->when($request->query('student_id'), fn ($q, $id) => $q->where('student_id', $id))
            ->orderByDesc('payment_date')
            ->paginate();

        return FeePaymentResource::collection($payments);
    }

    public function show(FeePayment $feePayment): FeePaymentResource
    {
        $feePayment->load(['feeCollection', 'student', 'collector']);

        return new FeePaymentResource($feePayment);
    }

    public function store(StoreFeePaymentRequest $request): JsonResponse
    {
        $payment = FeePayment::create([
            ...$request->validated(),
            'collected_by' => $request->user()->id,
        ]);

        $payment->load(['feeCollection', 'student', 'collector']);

        return response()->json([
            'message' => 'Payment recorded.',
            'data' => new FeePaymentResource($payment),
        ], 201);
    }

    public function destroy(FeePayment $feePayment): JsonResponse
    {
        $feePayment->delete();

        return response()->json(['message' => 'Payment deleted.']);
    }
}
