<?php

namespace App\Http\Controllers;

use App\YogaClass;
use App\Helpers\ControllerHelpers;
use Carbon\Carbon;
use App\Student;
use App\Payment;

class AssistanceTableController extends Controller
{
    /**
     * Given a date, will return the classes, students and payments done in the month from the date
     * to properly build an assistance graph
     */
    public function show($date)
    {
        $d1 = Carbon::parse($date);
        $d2 = Carbon::parse($d1);

        $d1->setDay(1);
        $d2->setDay($d1->daysInMonth);

        $classes = YogaClass::with('students:id')->where('date', '>=', $d1)->where('date', '<=', $d2)->get();

        $students = Student::select('id', 'name')->get();

        $payments = Payment::where('payed_at', '>=', $d1)->where('payed_at', '<=', $d2)->select('id', 'student_id', 'amount')->get();

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

        $yogaClass = YogaClass::firstOrCreate(['date' => $date]);

        $yogaClass->syncStudentsIfArrayContainsStudentIds($validatedData);

        return $yogaClass;
    }
}
