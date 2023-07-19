<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Field;
use App\Models\User;
use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ImportApiController extends Controller
{

    public function categories(Request $request)
    {

        return Category::count();
    }

    /*
        * Import Catgorties
     */
    public function importCategories(Request $request)
    {

        // dd($request->all());
            
        $validated = $request->validate([
            'categories'   => 'required|array'
        ]);


        $duplicates = [];

        try {


            error_log(count($request->categories));

            $chunks = collect($request->categories)->chunk(5)->toArray();

            // return count($chunks);
            foreach ($chunks as $index => $categories){

                foreach($categories as $chunk){
                    
                    $duplicate = Category::where('title', $chunk['title'])->first();

                    if($duplicate){

                        array_push($duplicates, $duplicate);

                    } else {

                        $category        =  new Category();
                        $category->id    =  $chunk['id'];
                        $category->title =  $chunk['title'];

                        if($chunk['image']){
                            $category->addMediaFromUrl($chunk['image'])
                                ->toMediaCollection();
                        }

                        $category->save();

                    }

                    
            
                }

            }


            return response()->json([
                'status'     => true,
                'duplicates' => $duplicates, 
                'category'   => Category::count()
            ], 201);


        } catch (\Exception $e) {

            return $e->getMessage();

            return response()->json(['status' => false, 'category'   => Category::count()], 422);

        }
    }

    public function subcategories(Request $request)
    {

        return SubCategory::with('fields')->get();
    }

    /*
        * Import Sub Catgorties
     */
    public function importSubCategories(Request $request)
    {

        // dd($request->all());
            
        $validated = $request->validate([
            'subcategories'   => 'required|array'
        ]);


        $duplicates = [];

        try {


            error_log(count($request->subcategories));

            $chunks = collect($request->subcategories)->chunk(5)->toArray();

            // return count($chunks);
            foreach ($chunks as $index => $subcategories){

                foreach($subcategories as $chunk){
                    
                    $duplicate = SubCategory::where('title', $chunk['title'])->first();

                    if($duplicate){

                        array_push($duplicates, $duplicate);

                    } else {

                        $subcategory                 =  new SubCategory();
                        $subcategory->id             =  $chunk['sub_category_id'];
                        $subcategory->category_id    =  $chunk['category_id'];
                        $subcategory->title          =  $chunk['title'];


                        // dd($chunk['fields']);
                        // return $chunk['fields'];
                        foreach($chunk['fields'] as $categoryField){

                            // return $field;
                            error_log('----- '. $categoryField['label'] . '----' . $chunk['sub_category_id']);
                           
                            $field                  = new Field();
                            $field->category_id     = $chunk['category_id'];
                            $field->sub_category_id = $chunk['sub_category_id'];
                            $field->label           = $categoryField['label'];
                            $field->type            = $categoryField['type'];
                            $field->is_price        = $categoryField['isPrice'];
                            $field->required        = $categoryField['required'];
                            $field->options         = $categoryField['options'];
                            $field->save();

                        }
            
                        $subcategory->save();

                    }

                    
            
                }

            }


            return response()->json([
                'status'        => true,
                'duplicates'    => $duplicates, 
                'subcategory'   => SubCategory::count(),
            ], 201);

        } catch (\Exception $e) {

            return $e->getMessage();

            return response()->json(['status' => false, 'subcategory'   => SubCategory::count()], 422);

        }
    }

    public function ads(Request $request)
    {
        // echo phpinfo() ;

        return Ad::all();
    }

    /*
        * Import Ads
     */
    public function importAds(Request $request)
    {

        // dd($request->all());
            
        $validated = $request->validate([
            'ads'   => 'required|array'
        ]);


        $duplicates = [];
        $noUser = [];

        try {


            error_log(count($request->ads));

            $noUser = collect($request->ads)->whereNull('user_id')->all();

            $chunks = collect($request->ads)->whereNotNull('user_id')
                        ->take(100)
                        ->chunk(20)
                        ->toArray();

            // return count($chunks);
            foreach ($chunks as $index => $ads){

                foreach($ads as $chunk){
                    
                    $duplicate = Ad::where('title', $chunk['title'])->first();
                    $fields    = json_encode($chunk['fields'], true);

                    if($duplicate){

                        array_push($duplicates, $duplicate);

                    } else {

                        $ad                     =  new Ad();
                        $ad->id                 =  $chunk['id'];
                        $ad->user_id            =  $chunk['user_id'];
                        $ad->category_id        =  $chunk['category_id'];
                        $ad->sub_category_id    =  $chunk['sub_category_id'];
                        $ad->title              =  $chunk['title'];
                        $ad->adId               =  $chunk['adId'];
                        $ad->description        =  $chunk['description'];
                        $ad->is_featured        =  $chunk['is_featured'];
                        $ad->price              =  $chunk['price'];
                        $ad->latitude           =  $chunk['latitude'];
                        $ad->longitude          =  $chunk['longitude'];
                        $ad->sold               =  $chunk['sold'];
                        $ad->fields             =  $fields;
                        $ad->expired_at         =  Carbon::parse($chunk['created_at'])->addDays(45);
                        // $ad->sold_to               =  $chunk['sold_to'];
                        $ad->status             =  "active";
                        $ad->created_at         = $chunk['created_at'];

                        if(count($chunk['images']) > 0){
                            foreach($chunk['images'] as $image){
                                $ad->addMediaFromUrl($image)
                                    ->toMediaCollection();
                            }

                        }

                        $ad->save();

                    }

                    
            
                }

            }


            return response()->json([
                'status'        => true,
                'duplicates'    => $duplicates, 
                'ads'           => Ad::count(),
                'noUser'        => $noUser,
            ], 201);

        } catch (\Exception $e) {

            return $e->getMessage();

            return response()->json(['status' => false, 'ads'   => Ad::count()], 422);

        }
    }

    /*
        * Import User Favourite Ads
     */
    public function importFavouriteAds(Request $request)
    {

        // dd($request->all());
            
        $validated = $request->validate([
            'ads'   => 'required|array'
        ]);


        try {


            error_log(count($request->ads));

            $chunks = collect($request->ads)->chunk(30)->toArray();

            // return count($chunks);
            foreach ($chunks as $index => $ads){

                foreach($ads as $chunk){
                        
                    \DB::table('ad_user')->insert([
                        'ad_id'     => $chunk['ad_id'],
                        'user_id'   => $chunk['user_id'],
                    ]);

                }

            }

            return response()->json([
                'status'        => true,
            ], 201);

        } catch (\Exception $e) {

            return $e->getMessage();

            return response()->json(['status' => false, ], 422);

        }
    }

    public function users(Request $request)
    {

        return User::count();
    }

    /*
        * Import Users
     */
    public function importUsers(Request $request)
    {

        // dd($request->all());
            
        // return $request->all();

        $validated = $request->validate([
            'users'   => 'required|array'
        ]);


        $duplicates = [];

        try {


            error_log(count($request->users));

            $chunks = collect($request->users)->chunk(500)->toArray();

            // return count($chunks);
            foreach ($chunks as $index => $users){

                foreach($users as $chunk){
                    
                    $duplicate = User::where('phone', $chunk['phone'])->first();

                    if($duplicate){
                        array_push($duplicates, $duplicate);
                    } else {

                        User::create([
                            'id'                        => $chunk['id'] ,            
                            'name'                      => $chunk['name'] ?: $chunk['email'],
                            'email'                     => $chunk['email'],
                            'phone'                     => $chunk['phone'],
                        
                            'dob'                       => $chunk['dob'] ?: Carbon::parse($chunk['dob'])->format('Y-m-d') ,
                            'gender'                    => $chunk['gender'] ?: "",
                            'avatar_url'                => $chunk['avatar_url'] ?: "",
                            'latitude'                  => $chunk['latitude'] ?: 0,
                            'longitude'                 => $chunk['longitude'] ?: 0,
                            
                            'status'                    =>  "active",
                            'password'                  => Str::random(20),
                        ]);
                    }

                    
            
                }

            }


            return response()->json(['status' => true, 'duplicates' => $duplicates], 201);

        } catch (\Exception $e) {

            return $e->getMessage();

            return response()->json(['status' => false], 422);

        }
    }
}
