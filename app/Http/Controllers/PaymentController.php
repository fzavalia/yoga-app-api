<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use App\Helpers\ControllerHelpers;

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
}
