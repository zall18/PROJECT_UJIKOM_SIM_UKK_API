<?php

namespace App\Http\Controllers;

use App\Models\CompetencyStandard;
use App\Models\Student;
use Illuminate\Http\Request;

class CompetencyStandardController extends Controller
{
    public function assessorCompetency(Request $request)
    {
        if ($request->user()->role == "assessor") {
            return response()->json([
                'competency' => CompetencyStandard::where('assessor_id', $request->user()->assessor->id)->get(),
            ]);
        }else{
            return response()->json([
                'message' => 'This is route for assessor'
            ], 404);
        }

    }

    public function assessorCompetitor(Request $request)
    {
        $competency = CompetencyStandard::where('id', $request->id)->first();

        if ($request->user()->role == "assessor") {
            return response()->json([
                'competitor' => Student::where('major_id', $competency->major_id)->with('user')->get(),

            ]);
        }else{
            return response()->json([
                'message' => 'This is route for assessor'
            ], 404);
        }


    }
}
