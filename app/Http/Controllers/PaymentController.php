<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use App\Helpers\RequestHelpers;

class PaymentController extends Controller
{
    public function show(Request $request, $id)
    {
        return RequestHelpers::show($request, $id, Payment::query());
    }

    public function list(Request $request)
    {
        return RequestHelpers::list($request, Payment::query());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:cash,credit_card',
            'amount' => 'required|int',
            'payed_at' => 'required|date',
            'student_id' => 'required|int',
        ]);

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
        ]);

        $payment = Payment::findOrFail($id);

        $payment->update($validatedData);

        return $payment;
    }

    public function delete($id)
    {
        $payment = Payment::findOrFail($id);

        $payment->delete();

        return $payment;
    }
}
