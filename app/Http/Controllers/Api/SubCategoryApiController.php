<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubCategoryApiController extends BaseController
{

	public function index(Category $category)
    {   
        try {

            $subcategories =  SubCategory::query()
                ->where('category_id', $category->id)
                ->orderBy('id', 'DESC')
                ->get();

            $data = [
                'status'  => true,
                'message' => 'Data fetched successfully.',
                'data'    => [
                    'category'       => $category,
                    'subcategories'  => $subcategories
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