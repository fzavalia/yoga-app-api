<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use App\Traits\QueryWhere;
use App\Traits\QueryOrder;

class PaymentController extends Controller
{
    use QueryWhere, QueryOrder;

    public function show($id)
    {
        $payment = Payment::with('student')->findOrFail($id);

        return $payment;
    }

    public function list(Request $request)
    {
        $query = Payment::query();

        $this->where($request, $query, ['student_id']);

        $this->order($request, $query);

        $payments = $query->get();

        return $payments;
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
