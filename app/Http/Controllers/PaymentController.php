<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;

class PaymentController extends Controller
{
    public function show($id)
    {
        $payment = Payment::findOrFail($id);

        return $payment;
    }

    public function list()
    {
        $payments = Payment::all();

        return $payments;
     }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string',
            'amount' => 'required|int',
            '' => 'string',
            'dni' => 'int'
        ]);

        $payment = Payment::create($validatedData);

        return $payment;
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'string',
            'email' => 'email',
            'phone_number' => 'string',
            'dni' => 'int'
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
