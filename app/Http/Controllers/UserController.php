<?php

namespace App\Http\Controllers;

use App\Models\CompetencyStandard;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function assessorHome(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'competency_count' => CompetencyStandard::where('assessor_id', $request->user()->assessor->id)->count(),
        ]);
    }
}
