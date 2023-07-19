<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Category;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryApiController extends BaseController
{

	public function index(){

        try {

            $categories = Category::query()
                ->orderBy('id', 'DESC')
                ->get();

            $data = [
                'status'  => true,
                'message' => 'Data fetched successfully.',
                'data'    => [
                    'categories'       => $categories,
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