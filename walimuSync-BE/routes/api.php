<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\AcademicCalendarController;
use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\Api\V1\DutyAssignmentController;
use App\Http\Controllers\Api\V1\ExamResultController;
use App\Http\Controllers\Api\V1\FeeCollectionController;
use App\Http\Controllers\Api\V1\FeePaymentController;
use App\Http\Controllers\Api\V1\SchoolClassController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\SubjectController;
use App\Http\Controllers\Api\V1\SubstitutionController;
use App\Http\Controllers\Api\V1\TeacherAbsenceController;
use App\Http\Controllers\Api\V1\TermController;
use App\Http\Controllers\Api\V1\TimetableSlotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Firebase Authentication Route
Route::post('firebase-login', [AuthController::class, 'firebaseLogin']);

// V1 API Routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authenticated user
    Route::get('/user', fn (Request $request) => $request->user());

    // Device tokens (FCM)
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
    Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);

    // Terms
    Route::get('/terms', [TermController::class, 'index']);
    Route::get('/terms/active', [TermController::class, 'active']);
    Route::get('/terms/{term}', [TermController::class, 'show']);

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/subjects/{subject}', [SubjectController::class, 'show']);

    // School Classes
    Route::get('/classes', [SchoolClassController::class, 'index']);
    Route::get('/classes/{schoolClass}', [SchoolClassController::class, 'show']);
    Route::get('/classes/{schoolClass}/students', [SchoolClassController::class, 'students']);
    Route::get('/classes/{schoolClass}/exam-results', [ExamResultController::class, 'byClass']);
    Route::get('/classes/{schoolClass}/exam-results/stats', [ExamResultController::class, 'classStats']);

    // Timetable
    Route::get('/timetable', [TimetableSlotController::class, 'index']);
    Route::get('/timetable/mine', [TimetableSlotController::class, 'myTimetable']);
    Route::post('/timetable', [TimetableSlotController::class, 'store']);
    Route::get('/timetable/{timetableSlot}', [TimetableSlotController::class, 'show']);
    Route::delete('/timetable/{timetableSlot}', [TimetableSlotController::class, 'destroy']);

    // Duty Assignments
    Route::get('/duties', [DutyAssignmentController::class, 'index']);
    Route::get('/duties/mine', [DutyAssignmentController::class, 'myDuties']);
    Route::post('/duties', [DutyAssignmentController::class, 'store']);
    Route::get('/duties/{dutyAssignment}', [DutyAssignmentController::class, 'show']);
    Route::put('/duties/{dutyAssignment}', [DutyAssignmentController::class, 'update']);
    Route::delete('/duties/{dutyAssignment}', [DutyAssignmentController::class, 'destroy']);

    // Teacher Absences
    Route::get('/absences', [TeacherAbsenceController::class, 'index']);
    Route::post('/absences', [TeacherAbsenceController::class, 'store']);
    Route::get('/absences/{teacherAbsence}', [TeacherAbsenceController::class, 'show']);
    Route::delete('/absences/{teacherAbsence}', [TeacherAbsenceController::class, 'destroy']);

    // Substitutions (Cover Lessons)
    Route::get('/substitutions', [SubstitutionController::class, 'index']);
    Route::get('/substitutions/mine', [SubstitutionController::class, 'myCoverLessons']);
    Route::post('/substitutions', [SubstitutionController::class, 'store']);
    Route::get('/substitutions/{substitution}', [SubstitutionController::class, 'show']);
    Route::delete('/substitutions/{substitution}', [SubstitutionController::class, 'destroy']);

    // Academic Calendar
    Route::apiResource('calendar', AcademicCalendarController::class)->parameters([
        'calendar' => 'academicCalendar',
    ]);

    // Students
    Route::get('/students/{student}/payments', [StudentController::class, 'payments']);
    Route::get('/students/{student}/exam-results', [StudentController::class, 'examResults']);
    Route::apiResource('students', StudentController::class);

    // Fee Collections
    Route::get('/fee-collections/mine', [FeeCollectionController::class, 'myCollections']);
    Route::get('/fee-collections/{feeCollection}/payments', [FeeCollectionController::class, 'payments']);
    Route::apiResource('fee-collections', FeeCollectionController::class);

    // Fee Payments
    Route::get('/fee-payments', [FeePaymentController::class, 'index']);
    Route::post('/fee-payments', [FeePaymentController::class, 'store']);
    Route::get('/fee-payments/{feePayment}', [FeePaymentController::class, 'show']);
    Route::delete('/fee-payments/{feePayment}', [FeePaymentController::class, 'destroy']);

    // Announcements
    Route::get('/announcements/for-teacher', [AnnouncementController::class, 'forTeacher']);
    Route::apiResource('announcements', AnnouncementController::class);

    // Exam Results
    Route::post('/exam-results/bulk', [ExamResultController::class, 'bulkStore']);
    Route::get('/exam-results/my-class', [ExamResultController::class, 'myClassResults']);
    Route::apiResource('exam-results', ExamResultController::class);
});
