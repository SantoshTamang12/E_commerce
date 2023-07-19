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

class UserAdApiController extends BaseController
{

	public function index(){


        try {
            $user = auth('api')->user();

            $ads  = Ad::query()
                    ->where('user_id', $user->id) 
                    ->with('seller:id,name,email')               
                    ->orderBy('id', 'DESC')
                    ->get();

            $data = [
                'status'  => true,
                'message' => 'Ads fetched successfully.',
                'data'    => [
                    'ads'           => $ads,
                    // 'current_page'  => $ads->currentPage(),
                    
                    // 'prev_page_url' => $ads->previousPageUrl(),
                    // 'next_page_url' => $ads->nextPageUrl(),
                    // 'total'         => $ads->total(),
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

    /**
     * Store A Specific Resource 
     * @param Request
    **/
    public function store(Request $request)
    {   
        

        $validator = Validator::make($request->only('category_id', 'subcategory_id', 'title', 'description', 'is_featured', 'price', 'position', 'latitude', 'longitude'), [
            'category_id'            => 'required|exists:categories,id',
            'subcategory_id'         => 'required|exists:sub_categories,id',
            'title'               => 'required|string|unique:ads',
            'description'         => 'required',
            'is_featured'         => 'required|boolean',
            'price'               => 'required|numeric',
            'position'            => 'required',
            'latitude'            => 'required|string',
            'longitude'           => 'required|string',
            'fields'              => 'nullable|array' 
        ]);

        if ($validator->fails()) {

            $response['message'] = $validator->messages()->first();
            $response['status']  = false;
            return $response;

        } 


        try {
            
            // dd($request->images, $request->all());
            $user  = auth('api')->user();

            $position =  json_encode(request()->position, true);
            $fields   =  json_encode(request()->fields, true);
            $ad       =  Ad::create([
                            'user_id'            => $user->id,
                            'category_id'        =>  $request->category_id,
                            'sub_category_id'    =>  $request->subcategory_id,
                            'title'              =>  $request->title,
                            'adId'                  =>  Str::random(20),
                            'description'           =>  $request->description,
                            'fields'                =>  $fields,
                            'is_featured'           =>  $request->is_featured ? true : false,
                            'price'                 =>  $request->price,
                            'position'              =>  $position,
                            'latitude'              =>  $request->latitude,
                            'longitude'             =>  $request->longitude,
                            'status'                => 'active',
                            'expired_at'            => Carbon::now()->addDays(45),
                        ]);

            if ($request->images) {
                try {
                    foreach($request->images as $image){
                        $ad->addMediaFromBase64($image)
                            ->toMediaCollection();
                    }
                } catch (FileDoesNotExist $e) {

                } catch (FileIsTooBig $e) {

                }
            }

            $data = [
                'status'  => true,
                'message' => 'Ad added successfully.',
                'data'    => [
                    'ad'            => $ad
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
    /**
     * Show A Specific Resource 
    **/
    public function show()
    {
        if(!request()->id){
            $response['status']   = false;
            $response['message']  = "Please pass an id.";
            return $response;
        }

        try {
            $user    = auth('api')->user();

            $userAd  = Ad::query()
                        ->with('seller:id,name,email,phone,gender,avatar_url,location,latitude,longitude,status', 'buyer:id,name,email')
                        ->findOrfail(request()->id);


            $data = [
                'status'  => true,
                'message' => 'Ad fetched successfully.',
                'data'    => [
                    'ad'            => $userAd
                ]
            ];

        } catch (\Exception $e){
            $data = [
                'status'   => false,
                'message'  => "Your request was not successfull.Please try again later."
            ];
        }

        return response()->json($data);
    }

    /**
     * Renew Ad
     * @param request()->id, Ad
     * @response @json 
    **/
    public function renew()
    {
        if(!request()->id){
            $response['status']   = false;
            $response['message']  = "Please pass an id.";
            return $response;
        }


        try {
            
            $user     = auth('api')->user();

            $updated  = Ad::query()
                        ->where('user_id', $user->id) 
                        ->findOrfail(request()->id)
                        ->update([
                            'status'      => 'active',
                            'is_featured' => true,
                            'expired_at'  => Carbon::now()->addDays(30),
                            // 'status'      => 'expired',
                            // 'is_featured' => false,
                            // 'expired_at'  => Carbon::now()->subDays(1)
                        ]);


            $data = [
                'status'  => true,
                'message' => 'Ad renewed successfully.',
                'data'    => [
                    'renewed'    => $updated,
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

    /**
     * Delete A Specific Resource 
    **/
    public function destroy()
    {
        if(!request()->id){
            $response['status']   = false;
            $response['message']  = "Please pass an id.";
            return $response;
        }

        try {
            $user   = auth('api')->user();

            $userAd  = Ad::query()
                        ->where('user_id', $user->id) 
                        ->findOrfail(request()->id);

            $userAd->clearMediaCollection();
            $userAd->delete();

            $data = [
                'status'  => true,
                'message' => 'Ad deleted successfully.',
                'data'    => []
            ];

        } catch (\Exception $e){
            $data = [
                'status'   => false,
                'message'  => "Your request was not successfull.Please try again later."
            ];
        }

        return response()->json($data);
    }
}