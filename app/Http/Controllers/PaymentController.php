<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use App\Helpers\ControllerHelpers;
use App\Helpers\ResponseHelpers;
use App\Helpers\DateHelpers;
use App\Student;
use App\YogaClass;

class PaymentController extends Controller
{
    public function show(Request $request, $id)
    {
        return ControllerHelpers::showForCurrentUser($request, $id, Payment::query());
    }

    public function list(Request $request)
    {
        return ControllerHelpers::listForCurrentUser($request, Payment::query());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:cash,credit_card',
            'amount' => 'required|int',
            'payed_at' => 'required|date',
            'student_id' => 'required|int',
            'invoiced' => 'boolean'
        ]);

        $validatedData['user_id'] = $request->user()->id;

        $payment = Payment::create($validatedData);

        return $payment;
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'type' => 'in:cash,credit_card',
            'amount' => 'int',
            'payed_at' => 'date',
            'student_id' => 'int',
            'invoiced' => 'boolean'
        ]);

        $payment = Payment::findOrFail($id);

        ControllerHelpers::validateUserCanHandleResource($request, $payment);

        $payment->update($validatedData);

        return $payment;
    }

    public function delete(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        ControllerHelpers::validateUserCanHandleResource($request, $payment);

        $payment->delete();

        return $payment;
    }

    public function total(Request $request)
    {
        $query = Payment::query();

        ControllerHelpers::listForCurrentUserQuery($request, $query);

        $total = $query->sum('amount');

        return ResponseHelpers::jsonResponse(['total' => $total]);
    }

    /**
     * Given a date, will return:
     * - Total payed in the month
     * - Total payed and invoiced in the month
     * - Students with how much they payed in the month and how many classes they attended
     */
    public function summary(Request $request, $date)
    {
        // Get date range from which the queries will be made

        $startingAndEndingDatesOfMonth = DateHelpers::getStartingAndEndingDatesOfMonth($date);

        $d1 = $startingAndEndingDatesOfMonth->start;
        $d2 = $startingAndEndingDatesOfMonth->end;

        // Get user for which the data will be queried

        $userId = $request->user()->id;

        $paymentsMadeInMonth = Payment::where('payed_at', '>=', $d1)
            ->where('payed_at', '<=', $d2)
            ->where('user_id', $userId)
            ->get();

        $totalPayedInGivenMonth = $paymentsMadeInMonth
            ->sum('amount');

        $totalInvoicedInGivenMonth = $paymentsMadeInMonth
            ->filter(function ($payment) {
                return $payment->invoiced;
            })
            ->sum('amount');

        $studentsWithTotalPayedInGivenMonth = Payment::where('user_id', $userId)
            ->where('payed_at', '>=', $d1)
            ->where('payed_at', '<=', $d2)
            ->get()
            ->groupBy('student_id')
            ->map(function ($payments) {
                return $payments->sum('amount');
            });

        $studentsWithTotalClassesAssistedInGivenMonth = YogaClass::with('students:id')
            ->where('user_id', $userId)
            ->where('date', '>=', $d1)
            ->where('date', '<=', $d2)
            ->get()
            ->reduce(function ($acc, $next) {
                $next->students->each(function ($student) use (&$acc) {
                    if (!isset($acc[$student->id])) {
                        $acc[$student->id] = 0;
                    }
                    $acc[$student->id] = $acc[$student->id] + 1;
                });
                return $acc;
            }, []);

        $studentsWithTotalPayedAndClassesAssistedInMonth = Student::where('user_id', $userId)
            ->select('id', 'name')
            ->get()
            ->map(function ($student) use ($studentsWithTotalPayedInGivenMonth, $studentsWithTotalClassesAssistedInGivenMonth) {
                $totalPayed = 0;
                if (isset($studentsWithTotalPayedInGivenMonth[$student->id])) {
                    $totalPayed = $studentsWithTotalPayedInGivenMonth[$student->id];
                }
                $classesAssisted = 0;
                if (isset($studentsWithTotalClassesAssistedInGivenMonth[$student->id])) {
                    $classesAssisted = $studentsWithTotalClassesAssistedInGivenMonth[$student->id];
                }
                $student->payed = $totalPayed;
                $student->assisted = $classesAssisted;
                return $student;
            });

        return ResponseHelpers::jsonResponse([
            'total' => $totalPayedInGivenMonth,
            'total_invoiced' => $totalInvoicedInGivenMonth,
            'students' => $studentsWithTotalPayedAndClassesAssistedInMonth
        ]);
    }
}
