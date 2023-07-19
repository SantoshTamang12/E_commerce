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

class FieldApiController extends BaseController
{

	public function index(Category $category, SubCategory $subcategory)
    {   
        try {

            $fields =  Field::query()
                ->where('category_id', $category->id)
                ->where('sub_category_id', $subcategory->id)
                ->orderBy('id', 'DESC')
                ->get();

            $data = [
                'status'  => true,
                'message' => 'Data fetched successfully.',
                'data'    => [
                    'category'       => $category,
                    'subcategory'    => $subcategory,
                    'fields'         => $fields,
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