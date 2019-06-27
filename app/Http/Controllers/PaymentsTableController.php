<?php

namespace App\Http\Controllers;

use App\Student;
use App\Payment;
use Illuminate\Http\Request;
use App\Helpers\DateHelpers;
use App\Helpers\ControllerHelpers;
use App\Helpers\ResponseHelpers;

class PaymentsTableController extends Controller
{
    /**
     * Given a date, will return:
     * - Total payed in the month
     * - Total payed and invoiced in the month
     * - Students with how much they payed in the month and how many classes they attended
     */
    public function __invoke(Request $request, $date)
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

        $studentsWithPaymentsMadeInGivenMonth = Student::with('payments')
            ->where('user_id', $userId)
            ->whereHas('payments', function ($query) use ($d1, $d2) {
                $query
                    ->where('payed_at', '>=', $d1)
                    ->where('payed_at', '<=', $d2);
            })
            ->select('id')
            ->get()
            ->keyBy('id');

        $studentsWithClassesAssistedInGivenMonth = Student::with('yogaClasses')
            ->where('user_id', $userId)
            ->whereHas('yogaClasses', function ($query) use ($d1, $d2) {
                $query
                    ->where('date', '>=', $d1)
                    ->where('date', '<=', $d2);
            })
            ->select('id')
            ->get()
            ->keyBy('id');

        $studentsWithTotalPayedInGivenMonth = $studentsWithPaymentsMadeInGivenMonth
            ->map(function ($student) {
                $payments = $student->payments;
                $student->total = $payments->sum('amount');
                unset($student->payments);
                return $student;
            });

        $studentsWithTotalClassesAssistedInGivenMonth = $studentsWithClassesAssistedInGivenMonth
            ->map(function ($student) {
                $yogaClasses = $student->yogaClasses;
                $student->total = $yogaClasses->count();
                unset($student->yogaClasses);
                return $student;
            });

        $studentsWithTotalPayedAndClassesAssistedInMonth = Student::where('user_id', $userId)
            ->select('id', 'name')
            ->get()
            ->map(function ($student) use ($studentsWithTotalPayedInGivenMonth, $studentsWithTotalClassesAssistedInGivenMonth) {
                $totalPayed = 0;
                if (isset($studentsWithTotalPayedInGivenMonth[$student->id])) {
                    $totalPayed = $studentsWithTotalPayedInGivenMonth[$student->id]->total;
                }
                $classesAssisted = 0;
                if (isset($studentsWithTotalClassesAssistedInGivenMonth[$student->id])) {
                    $classesAssisted = $studentsWithTotalClassesAssistedInGivenMonth[$student->id]->total;
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
