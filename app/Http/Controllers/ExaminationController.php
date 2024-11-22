<?php

namespace App\Http\Controllers;

use App\Models\CompetencyStandard;
use App\Models\Examination;
use Illuminate\Http\Request;

class ExaminationController extends Controller
{
    public function result(Request $request)
    {
        $standard = CompetencyStandard::where('id', $request->id)->withCount('competency_elements')->first();
        // Mendapatkan data ujian murid berdasarkan standard yang dipilih
        $examinations = Examination::where('standard_id', $request->id)->get();
        $active = 'examResult';

        // Mendapatkan daftar murid yang mengikuti ujian pada standar kompetensi ini
        $students = $examinations->groupBy('student_id')->map(function ($exams) use ($standard) {
            // Pastikan total elemen dihitung langsung dari data relasi
            $totalElements = $standard->competency_elements->count();

            // Hitung elemen kompeten secara unik berdasarkan element_id
            $completedElements = $exams->where('status', 1)->unique('element_id')->count();

            // Menghitung nilai akhir dalam bentuk persentase
            $finalScore = $totalElements > 0 ? round(($completedElements / $totalElements) * 100) : 0;

            // Menentukan status kompeten atau tidak kompeten
            $status = $finalScore >= 75 ? 'Competent' : 'Not Competent';
            // dd([
            //     'total_elements' => $totalElements,
            //     'completed_elements' => $completedElements,
            //     'exams' => $exams->toArray(),
            // ]);

            return [
                'student_id' => $exams->first()->student_id,
                'student_name' => $exams->first()->student->user->full_name,
                'final_score' => $finalScore,
                'status' => $status,
            ];
        });


        // Kirim data ke tampilan
        return response()->json([
            'unit_title' => $standard->unit_title,
            'exam_result' => $students
        ]);

    }
}
