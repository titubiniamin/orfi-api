<?php

namespace App\Mail;

use App\Models\Payment\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuySubscription extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->subject('Buy Subscription Notification from Orfi Application')
            ->markdown('emails.buy-subscription',['subscription'=>$this->subscription]);
    }
}
