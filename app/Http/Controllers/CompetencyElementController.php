<?php

namespace App\Http\Controllers;

use App\Models\CompetencyElement;
use App\Models\CompetencyStandard;
use App\Models\Examination;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompetencyElementController extends Controller
{
    public function examAndStatus(Request $request)
    {

        if ($request->user()->role == "assessor") {
            $data['elements'] = CompetencyElement::where('competency_standard_id', $request->id)->get();
            $data['standard'] = CompetencyStandard::where('id', $request->id)->first();
            $data['active'] = 'examResultReport';

            $standard = CompetencyStandard::where('assessor_id', $request->user()->assessor->id)
                ->withCount('competency_elements')
                ->first();

            // Tambahkan filter untuk satu siswa berdasarkan `student_id`
            $studentId = $request->student_id; // Pastikan `student_id` dikirim dalam request
            $exams = Examination::where('standard_id', $request->id)
                ->where('student_id', $studentId)
                ->get();

            $totalElements = $standard->competency_elements_count;
            $completedElements = $exams->where('status', 1)->count(); // Menghitung elemen yang statusnya kompeten
            $finalScore = round(($completedElements / $totalElements) * 100);
            $status = $finalScore >= 75 ? 'Competent' : 'Not Competent';
            $elementsStatus = $standard->competency_elements->sortBy('code')->map(function ($element) use ($exams) {
                $exam = $exams->firstWhere('element_id', $element->id);
                return [
                    'status' => $exam ? ($exam->status == 1 ? 'Kompeten' : 'Belum Kompeten') : 'Belum Dinilai',
                    'comments' => $exam ? $exam->comments : '-'
                ];
            });

            // Membuat data untuk siswa tertentu
            $data['student'] = [
                'student_id' => $studentId,
                'elements' => $elementsStatus,
                'final_score' => $finalScore,
                'status' => $status
            ];

            // Mengembalikan JSON dengan data siswa tertentu
            return response()->json($data['student']);
        }else{
            return response()->json([
                'message' => 'This is route for assessor'
            ], 404);
        }



    }

    public function gradingExam(Request $request)
    {

        if ($request->user()->role == "assessor") {
            $validator = Validator::make($request->all(), [
                'standard_id' => ['required'],
                'status' => ['required'],
                // 'comments' => ['required']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $element = CompetencyElement::where('id', $request->element_id)->first();

            if ($element) {
                $exam = Examination::where('student_id', $request->student_id)->where('element_id', $request->element_id)->first();

                if ($exam) {
                    Examination::where('student_id', $request->student_id)->where('element_id', $request->element_id)->update([
                        'status' => $request->status
                    ]);

                    return response()->json([
                        'message' => 'success to update status element'
                    ]);
                }else{
                    Examination::create([
                        'exam_date' => now(),
                        'student_id' => $request->student_id,
                        'assessor_id' => $request->user()->assessor->id,
                        'standard_id' => $request->standard_id,
                        'element_id' => $request->element_id,
                        'status' => $request->status,
                        'comments' => $request->comments
                    ]);

                    return response()->json([
                        'message' => 'success to input status element'
                    ]);
                }

            }else{
                return response()->json([
                    "message" => "Competency Element not found"
                ], 404);
            }
        }else{
            return response()->json([
                'message' => 'This is route for assessor'
            ], 404);
        }



    }
}
