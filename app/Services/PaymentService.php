<?php

namespace App\Services;

use App\Mail\BuySubscription;
use App\Models\Payment\Payment;
use App\Models\Payment\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentService
{

    /**
     * @param $user
     * @param $request
     * @return array
     */
    public static function setSSLPaymentInfo($user, Request $request): array
    {
        $post_data = array();
        $post_data['total_amount'] = $request->amount; # You can't pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid('ofri'); // tran_id must be unique

        if ($request->filled('tran_id')) {
            $post_data['tran_id'] = $request->tran_id;
            Cache::add('tran_id', $request->tran_id, now()->addMinute(5));// For Mobile apps redirect
        }

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $user->first_name . ' ' . $user->last_name;
        $post_data['cus_email'] = $user->email;
        $post_data['cus_add1'] = $user->address ?? '';
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $user->phone ?? '';
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "ORFI";
        $post_data['product_category'] = "Subscription";
        $post_data['product_profile'] = "Subscription Plan";

        # OPTIONAL PARAMETERS
//        $post_data['value_a'] = "ref001";
//        $post_data['value_b'] = "ref002";
//        $post_data['value_c'] = "ref003";
//        $post_data['value_d'] = "ref004";

        return $post_data;
    }

    public static function successTransaction(Request $request, $payment_details)
    {
        DB::transaction(function () use ($request, $payment_details) {
            self::updatePayment($request, $payment_details, 'processing');

            $subscription = Subscription::query()->where('payment_id', $payment_details->id);

            Subscription::query()->where('user_id', $subscription->first()->user_id)
                ->where('status', '!=', 'canceled')->update(['status' => 'inactive']); // Make previous all subscription Inactive.

            $subscription = $subscription->with(['subscriptionPlan', 'user:id,email'])->first();
            $addMethod = "add" . ucfirst($subscription->subscriptionPlan->duration_type); // Creating Carbon functions Like (addDay,addMonth,addYear).
            $subscription_duration = now()->$addMethod($subscription->subscriptionPlan->duration); // add subscription duration  with current date time.
            $subscription->update([
                'status' => 'active',
                'token' => Hash::make(now()->toDateTimeString().Str::random(5)) , // Generate token for subscription.
                'expired_at' => $subscription_duration
            ]);

            Mail::to($subscription->user->email)->send(new BuySubscription($subscription)); // Send Email to user.
        });
    }

    public static function failedOrCancelTransaction(Request $request, $payment_details, $status)
    {
        DB::transaction(function () use ($request, $payment_details, $status) {
            self::updatePayment($request, $payment_details, $status);
            Subscription::query()->where('payment_id', $payment_details->id)->delete();
        });
    }

    public static function updatePayment(Request $request, $payment_details, $status)
    {
        $payment_details->update([
                'status' => $status,
                'val_id' => $request->val_id,
                'bank_transaction_id' => $request->bank_tran_id,
                'store_amount' => $request->store_amount,
                'store_id' => $request->store_id,
                'card_type' => $request->card_type,
                'card_no' => $request->card_no,
                'card_issuer' => $request->card_issuer,
                'card_brand' => $request->card_brand,
                'card_sub_brand' => $request->card_sub_brand,
                'transaction_date' => $request->tran_date,
            ]
        );
    }

}
