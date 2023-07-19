<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FirebaseHelper;
use App\Helpers\VerificationHelpers;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthApiController extends BaseController
{
    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'phone'    => 'required|min:10|max:13|unique:users',
            'password' => 'required|min:8|string'
        ]);

        if ($validator->fails()) {

            $response['message'] = $validator->messages()->first();
            $response['status'] = false;
            return $response;
        } else {
            error_log(json_encode($request->all()));

            $user = new User([
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);
            $user->password = bcrypt($request->password);
            $user->status = "inactive";
            $user->save();

            $verificationCode  = VerificationHelpers::generateVerificationCode();;
            $user->otp         = $verificationCode;
            $user->otp_sent_at = Carbon::now();
            $user->save();

            // VerificationHelpers::sendVerificationCode($user, $verificationCode);

            $tokenResult       = $user->createToken('Personal Access Token');
            $token             = $tokenResult->token;
            $token->expires_at = Carbon::now()->addMonths(3);
            $token->save();

            $token = [
                'access_token' => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ];

            $user = $user->only(User::$apiAttributes);

            return response()->json([
                'status' => true,
                'data' => ['user' => $user, 'token' => $token]
            ]);
        }
    }

    public function tokenLogin(Request $request)
    {
        $auth = FirebaseHelper::ifFirebaseAuthenticated();

        $authenticated = FirebaseHelper::ifFirebaseAuthenticated();

        // return $authenticated;

        if (!$authenticated) {

            return response()->json([
                'status'     => false,
                'message'    =>  'Unauthorized!'
            ], 401);
        }



        try {

            $firebaseUser = $authenticated[0]->getUser($authenticated[1]);


            // dd($authenticated[0], $firebaseUser->uid);

            // dd($firebaseUser->uid, $firebaseUser->displayName, $firebaseUser->email);

            // $auth->updateUser($uid,
            //     [
            //         'displayName' => $firebaseUser->displayName,
            //         'email'       => $firebaseUser->email,
            //     ]
            // );
            // if(!is_null($imageUrl)){
            //     $auth->updateUser($uid,
            //         [
            //             'photoUrl' => FirebaseHelper::generatePicUrl($imageUrl),
            //         ]
            //     );
            // }

            $user = User::find($firebaseUser->uid);

            if ($user) {

                $user->phone        = $firebaseUser->phoneNumber;
                $user->device_token = $request->firebase_token;
                $user->save();
            } else {

                $user = new User([
                    'id'       => $firebaseUser->uid,
                    'name'     => Str::random(10),
                    'phone'    => $firebaseUser->phoneNumber,
                    'device_token' => $request->firebase_token,
                    'status'   => 'active',
                    'password' => bcrypt(Str::random(10)),
                ]);

                $user->save();
            }


            $tokenResult       = $user->createToken('Personal Access Token');
            $token             = $tokenResult->token;
            $token->expires_at = Carbon::now()->addMonths(3);
            $token->save();

            $token = [
                'access_token' => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ];

            $user = $user->only(User::$apiAttributes);

            return response()->json([
                'status' => true,
                'data' => ['user' => $user, 'token' => $token]
            ]);

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return $e;
        } catch (AuthException $e) {
        } catch (FirebaseException $e) {
        }
        return "Error!";
    }


    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone'       => 'required',
            'password'    => 'required',
            // 'remember_me' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {

            $response['message'] = $validator->messages()->first();
            $response['status'] = false;
            return $response;
        }

        try {

            $user = User::where('phone', $request->phone)->first();

            if (!$user)
                return response()->json([
                    'status' => false,
                    'message' => 'The phone is not found.',
                ], 401);

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'The phone or password is incorrect.'
                ], 401);
            } else {
                if ($user->status != "active")
                    return response()->json([
                        'status'  => false,
                        'message' => 'Login not permitted for this user.'
                    ]);

                $tokenResult = $user->createToken('Personal Access Token');
                $token       = $tokenResult->token;

                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addMonths(3);

                $token->save();
                $user  = $user->only(User::$apiAttributes);
                $token = [
                    'access_token' => $tokenResult->accessToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ];

                return response()->json([
                    'status' => true,
                    'data' => ['user' => $user, 'token' => $token]
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function completeProfile(Request $request)
    {
        $user = auth('api')->user();

        //validation
        $validator = Validator::make($request->all(), [
            'gender' => 'required',
            'email'  => 'nullable|sometimes|unique:users|email',
        ]);

        if ($validator->fails()) {
            $response['message'] = $validator->messages()->first();
            $response['status'] = false;
            return response()->json($response);
        }


        try {
            $user->gender = $request->gender;
            $user->email = $request->email;
            $user->bio = $request->bio;
            $user->health_info = $request->health_info;
            $user->save();
        } catch (\Exception $e) {
            return response()->json(
                [
                    $e,
                    'status' => false,
                    'message' => 'Could\'t update the profile'
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'title' => 'Success',
            'data' => $user->only(User::$apiAttributes)
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = auth('api')->user();

        $authenticated = FirebaseHelper::ifFirebaseAuthenticated();

        if (!$authenticated) {

            return response()->json([
                'status'     => false,
                'message'    =>  'Unauthorized!'
            ], 401);
        }

        //validation
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            $response['data']  = $validator->messages()->first();
            $response['status'] = false;
            return $response;
        }

        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Password updated successfully',
            'title'   => 'Success',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $array = [
            'Male',
            'Female',
            'Other',
            'Unspecified'
        ];

        $user = auth('api')->user();

        //validation
        $validator = Validator::make($request->only('name', 'gender', 'email', 'dob', 'avatar_url'), [
            'name'   => ['required', 'string'],
            'email'  => ['required', 'email'],
            'dob'    => ['required'],
            'gender' => ['required', 'string', Rule::in($array)],
            'avatar_url'  => 'required|string',
        ]);

        if ($validator->fails()) {
            $response['data']   = $validator->messages()->first();
            $response['status'] = false;
            return response()->json($response);
        }

        $loggedInUser = User::findOrfail($user->id);

        try {
            //update profile
            
            $loggedInUser->update([
                'name'        => $request->name,
                'gender'      => $request->gender,
                'email'       => $request->email,
                'dob'         => $request->dob,
                'avatar_url'  => $request->avatar_url,
            ]);

            // try {
            //     $user->clearMediaCollection();
            //     $user->addMediaFromRequest($request->image)
            //         ->toMediaCollection();

            // } catch (FileDoesNotExist $e) {

            // } catch (\Exception $e) {
            //     error_log($e);
            // }

        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Could\'t update the profile'
                ]
            );
        }

        // dd($user);

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'title'   => 'Success',
            'data'    => $loggedInUser->only(User::$apiAttributes),
        ]);
    }

    public function update(Request $request)
    {

        $user = auth('api')->user();

        $loggedInUser = User::findOrfail($user->id);

        try {

            $loggedInUser->update([
                'device_token'  => $request->device_token
            ]);
            //update profile
            
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Could\'t update the profile'
                ]
            );
        }
        return response()->json([
            'status'  => true,
            'message' => 'User params updated successfully',
            'title'   => 'Success',
            'data'    => $loggedInUser->only(User::$apiAttributes)
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'status'  => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function verifyPhone(Request $request)
    {
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'otp' => ['required']
        ]);

        if ($validator->fails()) {
            $response['data'] = $validator->messages()->first();
            $response['status'] = false;

            return response()->json($response);
        }

        // if (((int)Carbon::now()->diffInSeconds($user->otp_sent_at)) > 600) {
        //     return $this->returnError("Verification Expired, Please try again later", null, 500);
        // }
        if ($user->otp == $request->get('otp')) {
            //            if ($user->otp_verified_at == null) {
            $user->otp_verified_at = Carbon::now();
            $user->status = 'active';
            $user->save();
            //            }
            return response()->json([
                'status' => true,
                'message' => 'Successfully verified!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid code! Failed to verify'
            ]);
        }
    }

    public function resendVerificationCode()
    {
        try {
            $user = auth('api')->user();
            if ($user->otp_verified_at != null) {
                return $this->returnError("Verified already", null, 500);
            }
            if (((int)Carbon::now()->diffInSeconds($user->otp_sent_at)) < 180) {
                return $this->returnError("Verification sent already! Please try again in " . 180 - (int)Carbon::now()->diffInSeconds($user->otp_sent_at) . " seconds", null, 500);
            }

            $verificationCode = VerificationHelpers::generateVerificationCode();;
            $user->otp = $verificationCode;
            $user->otp_sent_at = Carbon::now();
            $user->save();
            VerificationHelpers::sendVerificationCode($user, $verificationCode);


            return \response()->json([
                'status' => true,
                'message' => 'Resent verification Code'
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Verification Code Sending failed'
                ]
            );
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = auth('api')->user();
            $user = User::findOrfail($user['id'])->only(User::$apiAttributes);

            return response()->json([
                'status' => true,
                'data'   => $user,
            ]);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validate = $request->validate([
                'phone' => 'required',
            ]);
            $customer = User::where('phone', $validate['phone'])->first();
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'The phone is not found',
                ], 401);
            } else {
                $customerId = $customer->id;

                $verificationCode = VerificationHelpers::generateVerificationCode();;
                $customer->otp    = $verificationCode;
                $customer->otp_sent_at = Carbon::now();
                $customer->save();
                VerificationHelpers::sendVerificationCode($customer, $verificationCode);

                return response()->json([
                    'status' => true,
                    'user_id' => $customerId
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validate = $request->validate([
                'otp'     => 'required',
                'user_id' => 'required',
                'new_password' => 'required',
            ]);
            $customer = User::where('id', $validate['user_id'])->where('otp', $validate['otp'])->first();
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP',
                ], 401);
            } else {
                if (((int)Carbon::now()->diffInSeconds($customer->otp_sent_at)) > 600) {
                    return $this->returnError("OTP Expired", null, 500);
                }
                $updateCustomer = $customer;
                $updateCustomer->password = bcrypt($request->new_password);
                $updateCustomer->update();

                return response()->json([
                    'status' => true,
                    'message' => 'Password Reset Successfully.'
                ], 201);
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}
