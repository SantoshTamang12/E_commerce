<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\SubCategory;
use Yajra\DataTables\DataTables;

class CategoryController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Categories';
        $this->resources = 'admin.categories.';
        parent::__construct();
        $this->route = 'categories.';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $info = $this->crudInfo();

        if (request()->ajax()) {
            $data = Category::query()
                ->take(10);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', '<input type="checkbox" data-id="{{ $id }}" class="selected">'
                )
                ->addColumn('thumbnail', function ($data) {
                    return $data->thumbnail;
                })
                ->addColumn('sub_category', '<a 
                    href="{{ route("categories.subcategories", $id) }}" 
                        data-id="{{ $id }}"
                        class="">
                        <i class="fa fa-eye mr-3"></i> 

                        <span >View Sub Categories</span>
                    </a>'
                )
                ->addColumn('action', function ($data) {
                    return view('templates.index_actions', [
                        'id' => $data->id, 'route' => $this->route,
                        'hideShow' => true ,'showModal' => false,
                    ])->render();
                })
                ->orderColumn('id', function ($query, $order) {
                     $query->orderBy('id', $order);
                })
                ->rawColumns(['checkbox', 'sub_category', 'action'])
                ->make(true);
        }

        // dd($info);

        return view($this->indexResource(), $info);
    }



    /**
     * the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $info = $this->crudInfo();
        // $info['showFooter'] = false;
        // return view($this->createResource(), $info);
    }

    /**
     * Store in Bulknnewly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function bulkStore(Request $request)
    {
        $validated =  $request->validate([
            'thumbnail'  => 'required|array',
            'title'      => 'required|array',
        ]);

        try {

            foreach($validated['title'] as $key => $title){

                // dd($title, $validated['thumbnail'] ?: '');
                $category = new Category();
                $category->title       = $title;

                if($validated['thumbnail'][$key] != 'null'){
                    $category->addMedia($validated['thumbnail'][$key])
                        ->toMediaCollection();
                }

                $category->save();
                
            }

            $data = [
                'status'   => true,
                'message'  => "Categories added successfully.",
                'status'   => 200,
            ];

        } catch (\Exception $e){
            $data = [
                'status'   => false,
                'message'  => $e->getMessage(),
                'status'   => 422,
            ];
        }
        

        return response()->json($data, $data['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Category $category)
    {
        
    }

     /**
     * the form for editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
       
    }

    /**
     * Delete Category
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {   
        try {

            $category->subCategories()->each(function($subcategory){
                $subcategory->delete();
            });

            $category->fields()->each(function($field){
                $field->delete();
            });

            $category->clearMediaCollection();
            $category->delete();


            $data = [
                'status'   => true,
                'message'  => "Category with subcategories and fields Deleted!.",
                'status'   => 200,
            ];
        } catch (\Exception $e){
            $data = [
                'status'   => false,
                'message'  => "Oops! The request wasnot successful.",
                'status'   => 422,
            ];
        }
        

        return response()->json($data, $data['status']);

    }

    /* Delete Category in bulk */
    public function bulkDelete(Request $request){
        $validated = $request->validate([
            'ids'   => 'required|array'
        ]);

        try {
            foreach($validated['ids'] as $id){

                $category = Category::findOrfail($id);

                $category->subCategories()->each(function($subcategory){
                    $subcategory->delete();
                });

                $category->fields()->each(function($field){
                    $field->delete();
                });

                $category->clearMediaCollection();
                $category->delete();

            }

            $data = [
                'status'   => true,
                'message'  => "Categories with subcategories and fields Deleted!.",
                'status'   => 200,
            ];

        } catch (\Exception $e){
            $data = [
                'status'   => false,
                'message'  => $e->getMessage(),
                'status'   => 422,
            ];
        }

        return response()->json($data, $data['status']);
    }

 
}
