<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Ad;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserFavouriteApiController extends BaseController
{
    public function index()
    {
        try {
            $favourites = auth('api')->user()->favourites;

            $data = [
                'status' => true,
                'message' => 'Status changed successfully.',
                'data'     => [
                    'favourite' => $favourites
                ]
            ];

        } catch (\Exception $e){
            $data = [
                'status'   => false,
                'message'  => $e->getMessage(),
                'data'     => []
            ];
        }

        return response()->json($data);

    }

	public function toggle()
    {
        if(!request()->id){
            $response['status']   = false;
            $response['message']  = "Please pass an id.";
            return $response;
        }

        try {

            $ad = Ad::findOrfail(request()->id);

            $setFavourite = auth('api')->user()->favourites()->toggle($ad->id);

            $isFavourite  = (count($setFavourite['attached']) <= 0) ? false : true;

            $data = [
                'status'    => true,
                'message'   => 'Status changed successfully.',
                'data'      => [
                    'favourite'    => $setFavourite,
                    'is_favourite' =>  $isFavourite
                ]
            ];

        } catch (\Exception $e){
            $data = [
                'status'   => false,
                'message'  => $e->getMessage()
            ];
        }

        return response()->json($data);

	}

    public function isFavourite()
    {
        if(!request()->id){
            $response['status']   = false;
            $response['message']  = "Please pass an id.";
            return $response;
        }

        try {
            $ad = Ad::findOrfail(request()->id);

            $isFavourite =  auth('api')->user()->favourites()->where('ad_id', $ad->id)->exists();

            $data = [
                'status'  => true,
                'message' => 'Data fetched successfully.',
                'data'    => [
                    'is_favourite' => $isFavourite
                ]
            ];

        } catch (\Exception $e){
            $data = [
                'status'   => false,
                'message'  => $e->getMessage()
            ];
        }

        return response()->json($data);

    }
}