<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getPaymentHistory(): JsonResponse
    {
        $paymentHistory = Payment::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get(['amount', 'card_type', 'transaction_date', 'transaction_id', 'updated_at'])
            ->take(20);
        return response()->json(['paymentHistory' => $paymentHistory, 'status' => 200]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPaymentStatus(Request $request): JsonResponse
    {
        $payment = Payment::query()
            ->where('transaction_id', $request->transaction_id)
            ->latest()
            ->first();
        return response()->json(['payment' => $payment, 'status' => 200]);
    }
}
