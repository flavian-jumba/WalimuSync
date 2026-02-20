<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreStudentRequest;
use App\Http\Requests\Api\V1\UpdateStudentRequest;
use App\Http\Resources\Api\V1\ExamResultResource;
use App\Http\Resources\Api\V1\FeePaymentResource;
use App\Http\Resources\Api\V1\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $students = Student::query()
            ->with('schoolClass')
            ->when($request->query('school_class_id'), fn ($q, $id) => $q->where('school_class_id', $id))
            ->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->query('search'), fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate();

        return StudentResource::collection($students);
    }

    public function show(Student $student): StudentResource
    {
        $student->load('schoolClass');

        return new StudentResource($student);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = Student::create($request->validated());
        $student->load('schoolClass');

        return response()->json([
            'message' => 'Student created.',
            'data' => new StudentResource($student),
        ], 201);
    }

    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $student->update($request->validated());
        $student->load('schoolClass');

        return response()->json([
            'message' => 'Student updated.',
            'data' => new StudentResource($student),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json(['message' => 'Student deleted.']);
    }

    public function payments(Student $student): AnonymousResourceCollection
    {
        $payments = $student->feePayments()
            ->with(['feeCollection', 'collector'])
            ->orderByDesc('payment_date')
            ->paginate();

        return FeePaymentResource::collection($payments);
    }

    public function examResults(Student $student, Request $request): AnonymousResourceCollection
    {
        $results = $student->examResults()
            ->with(['subject', 'term', 'recorder'])
            ->when($request->query('term_id'), fn ($q, $termId) => $q->where('term_id', $termId))
            ->when($request->query('exam_type'), fn ($q, $type) => $q->where('exam_type', $type))
            ->orderBy('subject_id')
            ->paginate();

        return ExamResultResource::collection($results);
    }
}
