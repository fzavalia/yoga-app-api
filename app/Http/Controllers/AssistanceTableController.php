<?php

namespace App\Http\Controllers;

use App\YogaClass;
use App\Helpers\ControllerHelpers;
use Carbon\Carbon;
use App\Student;
use App\Payment;
use Illuminate\Http\Request;

class AssistanceTableController extends Controller
{
    /**
     * Given a date, will return the classes, students and payments done in the month from the date
     * to properly build an assistance graph
     */
    public function show(Request $request, $date)
    {
        $d1 = Carbon::parse($date);
        $d2 = Carbon::parse($d1);

        $d1->setDay(1);
        $d2->setDay($d1->daysInMonth);

        $userId = $request->user()->id;

        $classes = YogaClass::with('students:id')
            ->where('user_id', $userId)
            ->where('date', '>=', $d1)
            ->where('date', '<=', $d2)
            ->get();

        $students = Student::select('id', 'name')
            ->where('user_id', $userId)
            ->get();

        $payments = Payment::where('user_id', $userId)
            ->where('payed_at', '>=', $d1)
            ->where('payed_at', '<=', $d2)
            ->select('id', 'student_id', 'amount')
            ->get();

        return ControllerHelpers::jsonResponse([
            'yoga_classes' => $classes,
            'payments' => $payments,
            'students' => $students
        ]);
    }

    public function updateStudentAssistance(Request $request, $date, $studentId)
    {
        $request->validate([
            'assisted' => 'required|boolean'
        ]);

        $yogaClass = $this->getClassForDate($request, $date);

        $yogaClassStudentIds = $yogaClass->students->map(function ($student) {
            return $student->id;
        });

        if ($request->assisted) {
            $yogaClassStudentIds->push($studentId);
        } else {
            $yogaClassStudentIds = $yogaClassStudentIds->filter(function ($sid) use ($studentId) {
                return $sid != $studentId;
            });
        }

        $yogaClass->students()->sync($yogaClassStudentIds->toArray());

        return ControllerHelpers::emptyResponse();
    }

    public function updateYogaClass(Request $request, $date)
    {
        $validatedData = $request->validate([
            'student_ids' => 'array',
            'student_ids.*' => 'int'
        ]);

        $yogaClass = $this->getClassForDate($request, $date);

        $yogaClass->syncStudentsIfArrayContainsStudentIds($validatedData);

        return ControllerHelpers::emptyResponse();
    }

    private function getClassForDate(Request $request, $date)
    {
        $userId = $request->user()->id;

        $yogaClass = YogaClass::where('date', $date)
            ->where('user_id', $userId)
            ->first();

        if ($yogaClass) {
            ControllerHelpers::validateUserCanHandleResource($request, $yogaClass);
        } else {
            $yogaClass = YogaClass::create(['date' => $date, 'user_id' => $request->user()->id]);
        }

        return $yogaClass;
    }
}
