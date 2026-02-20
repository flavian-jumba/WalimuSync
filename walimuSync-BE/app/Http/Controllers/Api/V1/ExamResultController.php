<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BulkStoreExamResultRequest;
use App\Http\Requests\Api\V1\StoreExamResultRequest;
use App\Http\Requests\Api\V1\UpdateExamResultRequest;
use App\Http\Resources\Api\V1\ExamResultResource;
use App\Models\ExamResult;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ExamResultController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $results = ExamResult::query()
            ->with(['student', 'subject', 'term', 'recorder'])
            ->when($request->query('student_id'), fn ($q, $id) => $q->where('student_id', $id))
            ->when($request->query('subject_id'), fn ($q, $id) => $q->where('subject_id', $id))
            ->when($request->query('term_id'), fn ($q, $id) => $q->where('term_id', $id))
            ->when($request->query('exam_type'), fn ($q, $type) => $q->where('exam_type', $type))
            ->when($request->query('grade'), fn ($q, $grade) => $q->where('grade', $grade))
            ->when(
                $request->query('school_class_id'),
                fn ($q, $classId) => $q->whereHas('student', fn ($sq) => $sq->where('school_class_id', $classId))
            )
            ->orderByDesc('created_at')
            ->paginate();

        return ExamResultResource::collection($results);
    }

    public function show(ExamResult $examResult): ExamResultResource
    {
        $examResult->load(['student', 'subject', 'term', 'recorder']);

        return new ExamResultResource($examResult);
    }

    public function store(StoreExamResultRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['recorded_by'] = $request->user()->id;

        if (empty($data['grade'])) {
            $data['grade'] = $this->calculateGrade((float) $data['score']);
        }

        $result = ExamResult::create($data);
        $result->load(['student', 'subject', 'term', 'recorder']);

        return response()->json([
            'message' => 'Exam result recorded.',
            'data' => new ExamResultResource($result),
        ], 201);
    }

    public function update(UpdateExamResultRequest $request, ExamResult $examResult): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['score']) && empty($data['grade'])) {
            $data['grade'] = $this->calculateGrade((float) $data['score']);
        }

        $examResult->update($data);
        $examResult->load(['student', 'subject', 'term', 'recorder']);

        return response()->json([
            'message' => 'Exam result updated.',
            'data' => new ExamResultResource($examResult),
        ]);
    }

    public function destroy(ExamResult $examResult): JsonResponse
    {
        $examResult->delete();

        return response()->json(['message' => 'Exam result deleted.']);
    }

    /**
     * Bulk store exam results for a class (class teacher workflow).
     */
    public function bulkStore(BulkStoreExamResultRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $recordedBy = $request->user()->id;

        $created = DB::transaction(function () use ($validated, $recordedBy) {
            $results = [];

            foreach ($validated['results'] as $entry) {
                $grade = $entry['grade'] ?? $this->calculateGrade((float) $entry['score']);

                $results[] = ExamResult::updateOrCreate(
                    [
                        'student_id' => $entry['student_id'],
                        'subject_id' => $validated['subject_id'],
                        'term_id' => $validated['term_id'],
                        'exam_type' => $validated['exam_type'],
                    ],
                    [
                        'score' => $entry['score'],
                        'grade' => $grade,
                        'remarks' => $entry['remarks'] ?? null,
                        'recorded_by' => $recordedBy,
                    ]
                );
            }

            return $results;
        });

        $collection = ExamResult::query()
            ->whereIn('id', collect($created)->pluck('id'))
            ->with(['student', 'subject', 'term', 'recorder'])
            ->get();

        return response()->json([
            'message' => count($created).' exam results recorded.',
            'data' => ExamResultResource::collection($collection),
        ], 201);
    }

    /**
     * Get exam results for all students in a class.
     */
    public function byClass(Request $request, SchoolClass $schoolClass): AnonymousResourceCollection
    {
        $results = ExamResult::query()
            ->with(['student', 'subject', 'term', 'recorder'])
            ->whereHas('student', fn ($q) => $q->where('school_class_id', $schoolClass->id))
            ->when($request->query('subject_id'), fn ($q, $id) => $q->where('subject_id', $id))
            ->when($request->query('term_id'), fn ($q, $id) => $q->where('term_id', $id))
            ->when($request->query('exam_type'), fn ($q, $type) => $q->where('exam_type', $type))
            ->orderBy('student_id')
            ->orderBy('subject_id')
            ->paginate();

        return ExamResultResource::collection($results);
    }

    /**
     * Get summary statistics for a class's exam results.
     */
    public function classStats(Request $request, SchoolClass $schoolClass): JsonResponse
    {
        $query = ExamResult::query()
            ->whereHas('student', fn ($q) => $q->where('school_class_id', $schoolClass->id))
            ->when($request->query('subject_id'), fn ($q, $id) => $q->where('subject_id', $id))
            ->when($request->query('term_id'), fn ($q, $id) => $q->where('term_id', $id))
            ->when($request->query('exam_type'), fn ($q, $type) => $q->where('exam_type', $type));

        $overall = (clone $query)->selectRaw('
            COUNT(*) as total_results,
            ROUND(AVG(score), 2) as average_score,
            ROUND(MIN(score), 2) as lowest_score,
            ROUND(MAX(score), 2) as highest_score
        ')->first();

        $gradeDistribution = (clone $query)
            ->select('grade', DB::raw('COUNT(*) as count'))
            ->groupBy('grade')
            ->orderBy('grade')
            ->pluck('count', 'grade');

        $subjectAverages = (clone $query)
            ->join('subjects', 'exam_results.subject_id', '=', 'subjects.id')
            ->select('subjects.name as subject', DB::raw('ROUND(AVG(exam_results.score), 2) as average_score'))
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('average_score')
            ->get();

        $topPerformers = (clone $query)
            ->join('students', 'exam_results.student_id', '=', 'students.id')
            ->select('students.id', 'students.name', 'students.admission_number', DB::raw('ROUND(AVG(exam_results.score), 2) as average_score'))
            ->groupBy('students.id', 'students.name', 'students.admission_number')
            ->orderByDesc('average_score')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => [
                'class' => [
                    'id' => $schoolClass->id,
                    'name' => $schoolClass->name,
                    'stream' => $schoolClass->stream,
                ],
                'summary' => $overall,
                'grade_distribution' => $gradeDistribution,
                'subject_averages' => $subjectAverages,
                'top_performers' => $topPerformers,
            ],
        ]);
    }

    /**
     * Get exam results for the authenticated class teacher's class.
     */
    public function myClassResults(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolClass = SchoolClass::where('teacher_id', $user->id)->first();

        if (! $schoolClass) {
            return response()->json(['message' => 'You are not assigned as a class teacher.'], 404);
        }

        $results = ExamResult::query()
            ->with(['student', 'subject', 'term', 'recorder'])
            ->whereHas('student', fn ($q) => $q->where('school_class_id', $schoolClass->id))
            ->when($request->query('subject_id'), fn ($q, $id) => $q->where('subject_id', $id))
            ->when($request->query('term_id'), fn ($q, $id) => $q->where('term_id', $id))
            ->when($request->query('exam_type'), fn ($q, $type) => $q->where('exam_type', $type))
            ->orderBy('student_id')
            ->paginate();

        return response()->json([
            'class' => [
                'id' => $schoolClass->id,
                'name' => $schoolClass->name,
                'stream' => $schoolClass->stream,
            ],
            'data' => ExamResultResource::collection($results)->response()->getData(true)['data'],
            'meta' => ExamResultResource::collection($results)->response()->getData(true)['meta'] ?? null,
        ]);
    }

    private function calculateGrade(float $score): string
    {
        return match (true) {
            $score >= 80 => 'A',
            $score >= 60 => 'B',
            $score >= 40 => 'C',
            $score >= 20 => 'D',
            default => 'E',
        };
    }
}
