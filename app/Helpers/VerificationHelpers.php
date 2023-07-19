<?php

/**
 * Created by PhpStorm.
 * User: Zwei
 * Date: 9/26/2019
 * Time: 6:02 PM
 */

namespace App\Helpers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class  VerificationHelpers
{
    public static function sendVerificationCode($user, $verificationCode)
    {
        if (env('APP_ENV') === "local") return;
        $args = http_build_query(array(
            'auth_token' => 'c75c687b697e82f28fe1e17ffc162cfe62b273103073528b101cc25dfcc0a762',
            'to' => $user->phone,
            'text' => 'Aayo! Hi ✋, ' . $verificationCode . ' is your verification code for Aayo App, Happy Riding !'
        ));
        $url = "https://sms.aakashsms.com/sms/v3/send/";

        # Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1); ///
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Response
        $response = curl_exec($ch);
        curl_close($ch);
    }

    static function generateVerificationCode()
    {
        if (env('APP_ENV') === "local")
            return 12345; //for testing purposes
        else return rand(10000, 99999);
    }
}
