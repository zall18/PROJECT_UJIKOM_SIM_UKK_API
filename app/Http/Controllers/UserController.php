<?php

namespace App\Http\Controllers;

use App\Models\CompetencyElement;
use App\Models\CompetencyStandard;
use App\Models\Examination;
use App\Models\Student;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function assessorHome(Request $request)
    {
        if ($request->user()->role == "assessor") {
            $data['elements'] = CompetencyElement::where('competency_standard_id', $request->id)->get();
            $data['standard'] = CompetencyStandard::where('id', $request->id)->first();
            $data['active'] = 'examResultReport';
            $standard = CompetencyStandard::where('assessor_id', $request->user()->assessor->id)->withCount('competency_elements')->first();
            $examinations = Examination::where('standard_id', $request->id)->get();
            $compe = Examination::select('standard_id', 'student_id')->distinct()->get()->count();
            $data['students'] = $examinations->groupBy('student_id')->map(function ($exams) use ($standard) {
                $totalElements = $standard->competency_elements_count;
                $completedElements = $exams->where('status', 1)->count(); // Menghitung elemen yang statusnya kompeten
                $finalScore = $totalElements > 0 ? round(($completedElements / $totalElements) * 100) : 0;
                return [
                    'final_score' => $finalScore,
                ];
            });

            $finalScores = $data['students']->pluck('final_score');
            // dd($finalScores);
            // Log::info('Final Scores:', $finalScores->toArray()); // Debugging log
            $avg = $finalScores->isNotEmpty() ? $finalScores->avg() : 0;

            return response()->json([
                'user' => $request->user(),
                'competency_count' => CompetencyStandard::where('assessor_id', $request->user()->assessor->id)->count(),
                'competitor_count' => $compe,
                'avg_last_score' => $avg,
                'student_active' => Student::all()->count()
            ]);

        }else{
            return response()->json([
                'message' => 'This is route for assessor'
            ], 404);
        }
    }
}
