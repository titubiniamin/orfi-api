<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
        Passport::tokensExpireIn(now()->addDays(7));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));


        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $frontendUrlWithToken = env('FRONT_END_APP_URL') . "email-verification?email_verify_url=" . $url;

            return (new MailMessage)
                ->subject('Verify Email Address from ORFI')
                ->greeting('Hello ' . $notifiable->name)
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $frontendUrlWithToken);
        });
    }
}
