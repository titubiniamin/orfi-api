@component('mail::message')
    # Hi, {{ $subscription->user->first_name }}. Your ({{ $subscription->subscriptionPlan->title }}) subscription plan has expired.

   This is to inform you that your subscription has expired today.
   Please renew your subscription to continue receiving our services.
    @component('mail::button', ['url' => env('FRONT_END_APP_URL').'dashboard/buy-subscription'])
        Renew your subscription
    @endcomponent

    Thanks for using our application!
    ORFI Application.
@endcomponent
