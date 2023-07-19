<?php
namespace App\Helpers;

use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\ServiceAccount;


class FirebaseHelper{

    public static function getFirebaseAuth()
    {   

        return Firebase::auth();
        
    }

    static function ifFirebaseAuthenticated(){

        $firebase_token = request()->header('firebase_token');

        if(!$firebase_token){
            return false;
        }
        
        $auth = self::getFirebaseAuth();
        
        $verifiedIdToken = $auth->verifyIdToken($firebase_token);

        $uid = $verifiedIdToken->claims()->get('sub');

        if(!$uid){
            return false;
        }

        return [$auth, $uid];
    }   

     
 }