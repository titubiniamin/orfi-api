<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Services\AccessTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * @param $provider
     * @return JsonResponse
     */

    public function redirectToProvider($provider): JsonResponse
    {
        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return response()->json([
            'url' => $url
        ]);
    }

    /**
     * @param $provider
     * @return JsonResponse|void
     */
    public function handleProviderCallback($provider)
    {
        $social_user = Socialite::driver($provider)->stateless()->user();


        if (isset($social_user)) {
            $name = $social_user->getName();
            $email = $social_user->getEmail();
            $provider_id = $social_user->getId();
            $avatar = $social_user->getAvatar();

            //Checking if the user is new
            $user = User::where('email', $email)->first();

            //If user is new then register
            if (!$user) {
                $user = new User();

                if (!empty($name)) {
                    $name = self::nameSplit($name);

                    $user->first_name = $name['first_name']; //first name
                    if (isset($name['last_name'])) {
                        $user->last_name = $name['last_name'];
                    }
                }

                $user->email = $email;
                $user->social_account = $provider;
                $user->social_id = $provider_id;
//                $user->avatar = $avatar;
                $user->email_verified_at = Carbon::now();
                $user->save();
            }
            // return Access Token
            return AccessTokenService::getAccessToken($user);
        }
    }

    /**
     * @return void
     */
    public function facebookDataDeletion(Request $request)
    {
        $data = self::parse_signed_request($request->signed_request);
        $user_id = $data['user_id'];
        $access_token = $data['oauth_token'];

        $user = new User();
        $user = $user->where(['social_account' => 'facebook', 'social_id' => $user_id])->first();
        if ($user) {
            $user->social_account = null;
            $user->social_id = null;
            $user->save();
        }

        $url = "https://graph.facebook.com/" . $user_id . "/permissions?access_token=" . $access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }

    /**
     * @param $name
     * @return array
     */
    private static function nameSplit($name): array
    {
        $name = trim($name);
        $last_name = (strpos($name, ' ') === false)
            ? ''
            : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));
        return array('first_name' => $first_name, 'last_name' => $last_name);
    }

    function parse_signed_request($signed_request)
    {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        $secret = env('FACEBOOK_CLIENT_SECRET'); // Use your app secret here

        // decode the data
        $sig = self::base64_url_decode($encoded_sig);
        $data = json_decode(self::base64_url_decode($payload), true);

        // confirm the signature
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

}
