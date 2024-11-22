<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetencyElementController;
use App\Http\Controllers\CompetencyStandardController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\UserController;
use App\Models\CompetencyElement;
use App\Models\CompetencyStandard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/assessor/home', [UserController::class, 'assessorHome']);
    Route::get('/assessor/competency', [CompetencyStandardController::class, 'assessorCompetency']);
    Route::get('/assessor/competency/{id}/student', [CompetencyStandardController::class, 'assessorCompetitor']);
    Route::get('/assessor/competency/{id}/student/{student_id}', [CompetencyElementController::class, 'examAndStatus']);
    Route::post('/assessor/competency/element/{element_id}/student/{student_id}', [CompetencyElementController::class, 'gradingExam']);
    Route::get('/assessor/competency-standard/{id}', [CompetencyStandardController::class, 'element']);
    Route::get('/assessor/exam-result/{id}', [ExaminationController::class, 'result']);
});
