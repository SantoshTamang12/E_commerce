<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Field;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Ad;

class AdApiController extends BaseController
{

    /**
     * List fresh ads
     *
     */
    public function freshAds()
    {
        try {
            $user = auth('api')->user();
            $adQuery  = Ad::query()

                ->where('sold', false)
                ->with('seller:id,name,email', 'buyer:id,name,email');

            if ($user) {
                $adQuery = $adQuery->where('user_id', '!=', $user->id);
            }
            $ads = $adQuery->latest()
                ->paginate(8);

            $data = [
                'status'  => true,
                'message' => 'Ads fetched successfully.',
                'data'       => $ads,
            ];
        } catch (\Exception $e) {
            $data = [
                'status'   => false,
                'message'  => $e->getMessage()
            ];
        }

        return response()->json($data);
    }


    /**
     * List ads based on location
     **/
    public function locationBasedAds()
    {
        $latitude  = request()->latitude;
        $longitude = request()->longitude;

        try {
            $user    = auth('api')->user();

            $ads     = Ad::query()
                // ->where('user_id', '!==', $user->id)
                ->where('sold', false)
                ->with('seller:id,name,email', 'buyer:id,name,email');

            $radius = config('constants.search_radius');

            if ($latitude && $longitude) {
                $ads = $ads->select('*')
                    ->selectRaw("(6371 * acos( cos( radians('" .$latitude ."') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('".$longitude."') ) + sin( radians('".$latitude."') ) * sin( radians(latitude) ) ) ) AS distance")
                    ->whereRaw("(6371 * acos( cos( radians('" .$latitude ."') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('".$latitude."') ) + sin( radians('" .$latitude ."') ) * sin( radians(latitude) ) ) ) <= '".$radius."'")
                    ->orderBy('distance', 'asc')
                    ->distinct('id');
            }



            $data = [
                'status'  => true,
                'message' => 'Ad fetched successfully.',
                'data'    => [
                    'ads'            => $ads->latest()->get()
                ]
            ];
        } catch (\Exception $e) {
            dd($e->getMessage());
            $data = [
                'status'   => false,
                'message'  => "Your request was not successfull. Please try again later."
            ];
        }

        return response()->json($data);
    }

    /**
     * List based on category & subcategory
     *
     */
    public function getAdsWhichHasCategoryAndSubcategory()
    {

        if (!request()->category_id) {
            $response['status']   = false;
            $response['message']  = "Please provide a category.";
            return $response;
        }

        if (!request()->subcategory_id) {
            $response['status']   = false;
            $response['message']  = "Please provide a subcategory.";
            return $response;
        }

        try {

            $user = auth('api')->user();

            $ads  = Ad::query()
                // ->when($user, function($q) use($user) {
                //     return $q->where('user_id', '!=', $user->id);
                // })
                ->where('category_id', request()->category_id)
                ->where('sub_category_id', request()->subcategory_id)
                ->where('sold', false)
                ->with('seller:id,name,email', 'buyer:id,name,email')
                ->latest()
                ->paginate(10);

            $data = [
                'status'  => true,
                'message' => 'Ads fetched successfully.',
                'data'    => $ads,
            ];
        } catch (\Exception $e) {
            $data = [
                'status'   => false,
                'message'  => $e->getMessage()
            ];
        }

        return response()->json($data);
    }


    /**
     * Search Ads
     *
     */
    public function search(Request $request)
    {
        try {
            $user = auth('api')->user();
            $adQuery  = Ad::query()
                ->where('title', 'like', '%' . $request->get('query') . '%')
                ->where('sold', false)
                ->with('seller:id,name,email', 'buyer:id,name,email', 'category:id,title', 'subcategory:id,title');

            if ($user) {
                $adQuery = $adQuery->where('user_id', '!=', $user->id);
            }
            $ads = $adQuery->latest()
                ->paginate(8);

            $data = [
                'status'  => true,
                'message' => 'Ads fetched successfully.',
                'data'       => $ads,
            ];
        } catch (\Exception $e) {
            $data = [
                'status'   => false,
                'message'  => $e->getMessage()
            ];
        }

        return response()->json($data);
    }
}
