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

    public function updateYogaClass(Request $request, $date)
    {
        $validatedData = $request->validate([
            'student_ids' => 'array',
            'student_ids.*' => 'int'
        ]);

        $userId = $request->user()->id;

        $yogaClass = YogaClass::where('date', $date)
            ->where('user_id', $userId)
            ->first();

        if ($yogaClass) {
            ControllerHelpers::validateUserCanHandleResource($request, $yogaClass);
        } else {
            $yogaClass = YogaClass::create(['date' => $date, 'user_id' => $request->user()->id]);
        }

        $yogaClass->syncStudentsIfArrayContainsStudentIds($validatedData);

        return $yogaClass;
    }
}
