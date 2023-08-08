<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\SSLPaymentRequest;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Payment\Payment;
use App\Models\Payment\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cache;

class SslCommerzPaymentController extends Controller
{

    protected $success_url;
    protected $failed_url;
    protected $cancel_url;

    public function __construct()
    {
        $this->success_url = env('FRONT_END_APP_URL') . 'dashboard/payment/success';
        $this->failed_url = env('FRONT_END_APP_URL') . 'dashboard/payment/failed';
        $this->cancel_url = env('FRONT_END_APP_URL') . 'dashboard/payment/cancel';
    }


    public function sslPayment(SSLPaymentRequest $request)
    {
        $subscriptionPlanId = $request->subscription_plan_id;
        $amount = $request->amount;

        $subscription_plan = SubscriptionPlan::find($subscriptionPlanId);
        $user = Auth::user();
        if ($subscription_plan->amount != $amount) {
            return response()->json(['message' => 'Subscription amount and your payable amount are not same.']);
        }

        # Set Useful data for SSL Payment.
        $sslPaymentInfo = PaymentService::setSSLPaymentInfo($user, $request);

        DB::transaction(function () use ($user, $sslPaymentInfo, $subscriptionPlanId) {
            $payment = Payment::query()
                ->updateOrCreate(
                    ['transaction_id' => $sslPaymentInfo['tran_id']],
                    [
                        'user_id' => $user->id,
                        'amount' => $sslPaymentInfo['total_amount'],
                        'status' => 'pending',
                        'transaction_id' => $sslPaymentInfo['tran_id'],
                        'currency' => $sslPaymentInfo['currency'],
                    ]);
            Subscription::create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'subscription_plan_id' => $subscriptionPlanId,
            ]);
        });

        $sslCommerz = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payment gateway here )
        $payment_options = $sslCommerz->makePayment($sslPaymentInfo, 'checkout', 'json');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function success(Request $request) : RedirectResponse
    {
//        echo "Transaction is Successful";
        $this->changeRedirectUrl($request->input('tran_id'));
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');


        $sslc = new SslCommerzNotification();

        #Check order status in order table against the transaction id or order id.
        $payment_details = Payment::query()
            ->where('transaction_id', $tran_id)
            ->first();

        if ($payment_details->status == 'pending') {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

            if ($validation == TRUE) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
                in order table as processing or complete.
                Here you can also send sms or email for successful transaction to customer
                */
                PaymentService::successTransaction($request,$payment_details);
//                echo "<br >Transaction is successfully completed";
                return Redirect::to($this->success_url);
            } else {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel and Translation validation failed.
                Here you need to update order status as failed in order table.
                */
                PaymentService::failedOrCancelTransaction($request,$payment_details, 'failed');
//                echo "validation Fail";
                return Redirect::to($this->failed_url);
            }
        } else if ($payment_details->status == 'processing' || $payment_details->status == 'completed') {
            /*
             That means through IPN Payment status already updated. Now you can just show the customer that transaction is completed. No need to update database.
             */
            return Redirect::to($this->success_url);
        } else {
            #That means something wrong happened. You can redirect customer to your product page.
//            echo "Invalid Transaction";
            return Redirect::to($this->failed_url);
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function fail(Request $request) : RedirectResponse
    {
        ;
        $this->changeRedirectUrl($request->input('tran_id'));
        $tran_id = $request->input('tran_id');

        $payment_details = Payment::query()
            ->where('transaction_id', $tran_id)
            ->first();

        if ($payment_details->status == 'pending') {
            PaymentService::failedOrCancelTransaction($request,$payment_details, 'failed');
//            echo "Transaction is Failed";
            return Redirect::to($this->failed_url);
        } else if ($payment_details->status == 'processing' || $payment_details->status == 'complete') {
//            echo "Transaction is already Successful";
            return Redirect::to($this->success_url);
        } else {
//            echo "Transaction is Invalid";
            return Redirect::to($this->failed_url);
        }

    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancel(Request $request) : RedirectResponse
    {
        $this->changeRedirectUrl($request->input('tran_id'));
        $tran_id = $request->input('tran_id');

        $payment_details = Payment::query()
            ->where('transaction_id', $tran_id)
            ->first();

        if ($payment_details->status == 'pending') {
            PaymentService::failedOrCancelTransaction($request,$payment_details, 'canceled');
            return Redirect::to($this->cancel_url);
        } else if ($payment_details->status == 'processing' || $payment_details->status == 'complete') {
            return Redirect::to($this->success_url);
        } else {
            return Redirect::to($this->failed_url);
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function ipn(Request $request)
    {
        $this->changeRedirectUrl($request->input('tran_id'));
        #Received all the payment information from the gateway
        if ($request->input('tran_id')) #Check translation id is posted or not.
        {

            $tran_id = $request->input('tran_id');

            #Check order status in order table against the transaction id or order id.
            $payment_details = Payment::query()
                ->where('transaction_id', $tran_id)
                ->first();

            if ($payment_details->status == 'pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($request->all(), $tran_id, $payment_details->amount, $payment_details->currency);
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as processing or complete.
                    Here you can also send sms or email for successful transaction to customer
                    */
                    PaymentService::successTransaction($request,$payment_details);

//                    echo "Transaction is successfully completed";
                    return Redirect::to($this->success_url);
                } else {
                    /*
                    That means IPN worked, but Translation validation failed.
                    Here you need to update order status as failed in order table.
                    */
                    PaymentService::failedOrCancelTransaction($request,$payment_details, 'failed');

//                    echo "validation Fail";
                    return Redirect::to($this->failed_url);
                }

            } else if ($payment_details->status == 'processing' || $payment_details->status == 'completed') {

                #That means Payment status already updated. No need to update database.

//                echo "Transaction is already successfully completed";
                return Redirect::to($this->success_url);
            } else {
                #That means something wrong happened. You can redirect customer to your product page.

//                echo "Invalid Transaction";
                return Redirect::to($this->failed_url);
            }
        } else {
//            echo "Invalid Data";
            return Redirect::to($this->failed_url);
        }
    }




    protected function changeRedirectUrl($tran_id)
    {
        if (Cache::pull('tran_id') == $tran_id) {
            $this->success_url = env('MOBILE_EXPO_APP_URL') . '?status=success';
            $this->failed_url = env('MOBILE_EXPO_APP_URL') . '?status=failed';
            $this->cancel_url = env('MOBILE_EXPO_APP_URL') . '?status=cancel';
        }
    }


}
