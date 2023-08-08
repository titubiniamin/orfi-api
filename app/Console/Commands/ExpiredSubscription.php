<?php

namespace App\Console\Commands;

use App\Models\Payment\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ExpiredSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expired:subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send an email to the user when the subscription is expired.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subscriptions = Subscription::query()
            ->where('status', 'active')->whereDate('expired_at', today())
            ->with(['user:id,first_name,email','subscriptionPlan:id,title'])
            ->get();
        /********************   Sending Mail to User. **************************/
        foreach ($subscriptions as $subscription) {
            Mail::to($subscription->user->email)->send(new \App\Mail\ExpiredSubscription($subscription));
            $this->info('Email sent to ' . $subscription->user->email.' for '.$subscription->subscriptionPlan->title.' subscription plan expired.');
        }

        return 0;
    }
}
