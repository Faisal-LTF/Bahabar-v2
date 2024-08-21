<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Process payment
    public function process(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'status' => 'required|in:berhasil,gagal',
        ]);

        $payment = new Payment();
        $payment->user_id = auth()->id();
        $payment->amount = $validated['amount'];
        $payment->status = $validated['status'];
        $payment->save();

        return response()->json(['message' => 'Payment processed successfully']);
    }
}
