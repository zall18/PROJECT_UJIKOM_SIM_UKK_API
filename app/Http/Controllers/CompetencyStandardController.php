<?php

namespace App\Http\Controllers;

use App\Models\CompetencyStandard;
use App\Models\Student;
use Illuminate\Http\Request;

class CompetencyStandardController extends Controller
{
    public function assessorCompetency(Request $request)
    {
        return response()->json([
            'competency' => CompetencyStandard::where('id', $request->user()->assessor->id)->get(),
        ]);
    }

    public function assessorCompetitor(Request $request)
    {
        $competency = CompetencyStandard::where('id', $request->id)->first();

        return response()->json([
            'competitor' => Student::where('major_id', $competency->major_id)->with('user')->get(),

        ]);
    }
}
