<?php

namespace App\Http\Controllers;

use App\Models\CompetencyStandard;
use App\Models\Examination;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {

        // Ambil student yang sedang login
        $student = Auth::user()->student;

        // Ambil semua competency standard berdasarkan jurusan student
        $standards = CompetencyStandard::where('major_id', $student->major_id)->with('competency_elements')->get();

        // Hasil akhir untuk menyimpan status setiap competency standard
        $statusSummary = $standards->map(function ($standard) use ($student) {
            // Ambil semua examination terkait competency standard ini dan student login
            $examinations = Examination::where('standard_id', $standard->id)
                ->where('student_id', $student->id)
                ->get();

            // Hitung jumlah elemen dan status
            $totalElements = $standard->competency_elements->count();
            $completedElements = $examinations->where('status', 1)->unique('element_id')->count();
            // Hitung nilai akhir
            $finalScore = $totalElements > 0 ? round(($completedElements / $totalElements) * 100) : 0;
            // Tentukan status
            $status = $finalScore >= 90 ? 'Competent' : 'Not Competent';
            return [
                'unit_title' => $standard->unit_title,
                'status' => $status,
                'final_score' => $finalScore,
            ];
        });

        $notCompetentCount = 0;
        foreach ($statusSummary as $key => $item) {
            if ($item['status'] == 'Not Competent') {
                $notCompetentCount++;
            }
        }
        $data['notCompetentCount'] = $notCompetentCount;
        $data['standards'] = $standards;
        $data['active'] = 'dashboard';
        $data['statusSummary'] = $statusSummary;

        return response()->json([
            'notCompetentCount' => $notCompetentCount,
            'standardsCount' => $standards->count(),
            'user' => $request->user()
        ]);

    }

    private function conversi($grade)
    {
        if ($grade >= 91 && $grade <= 100) {
            return 'Very Competent';
        } else if ($grade >= 75 && $grade <= 90) {
            return 'Competent';
        } else if ($grade >= 61 && $grade <= 74) {
            return 'Quite Competent';
        } else if ($grade <= 60) {
            return 'Not Competent';
        }
    }

    public function resultStudent(Request $request)
    {
        // Ambil student yang sedang login
        $student = Auth::user()->student;

        // Ambil semua competency standard berdasarkan jurusan student
        $standards = CompetencyStandard::where('major_id', $student->major_id)->with('competency_elements')->get();

        // Hasil akhir untuk menyimpan status setiap competency standard
        $statusSummary = $standards->map(function ($standard) use ($student) {
            // Ambil semua examination terkait competency standard ini dan student login
            $examinations = Examination::where('standard_id', $standard->id)
                ->where('student_id', $student->id)
                ->get();

            // Hitung jumlah elemen dan status
            $totalElements = $standard->competency_elements->count();
            $completedElements = $examinations->where('status', 1)->unique('element_id')->count();

            // Hitung nilai akhir
            $finalScore = $totalElements > 0 ? round(($completedElements / $totalElements) * 100) : 0;

            // Tentukan status
            $status = $this->conversi($finalScore);

            return [
                'unit_title' => $standard->unit_title,
                'status' => $status,
                'final_score' => $finalScore,
            ];
        });

        $data['active'] = 'examResult';
        $data['statusSummary'] = $statusSummary;

        return response()->json([
            'statusSummary' => $statusSummary
        ]);
    }

    public function studentProfile(Request $request)
    {
        $id = $request->user()->id;
        $student = Student::where('user_id', $id)->with(['user', 'major'])->first();
        return $student;
    }
}
