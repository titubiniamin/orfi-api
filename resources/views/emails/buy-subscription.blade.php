@component('mail::message')
    # Thank you for your subscription.

   You have successfully subscribed to our **ORFI {{ $subscription->subscriptionPlan->title }}**. This Subscription will be active until **{{ \Carbon\Carbon::make($subscription->expired_at)->format('d M, Y') }}**.\
    \
    You can update your subscription anytime from our system. For more update, please contact our help support.

    Thanks for using our application!
    ORFI Application.
@endcomponent
