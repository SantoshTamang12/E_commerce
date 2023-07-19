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

class SubCategoryController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Subcategories';
        $this->resources = 'admin.subcategories.';
        parent::__construct();
        $this->route = 'subcategories.';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Category $category)
    {
        $info = $this->crudInfo();

        $info['category'] = $category->id;

        if (request()->ajax()) {
            $category_id   = $category->id;
            $data = SubCategory::query()
                ->where('category_id', $category_id)
                ->take(10);


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', '<input type="checkbox" data-id="{{ $id }}" class="selected">'
                )
                ->addColumn('fields', function ($data) use ($category_id) {
                    return '
                        <a href="/categories/'.$category_id.'/subcategories/'.$data->id.'/fields" class="mr-3">
                            <i class="fa fa-eye mr-2"></i>
                            View Fields
                        </a>

                        <button type="button" 
                            data-id="'. $data->id .'"
                            class="add-field btn btn-primary align-items-center"
                            data-toggle="modal"
                            data-target="#field-modal" data-whatever="@mdo">
                            <i class="fa fa-plus mr-2"></i> 
                            <span >Add Fields</span>
                        </button>';
                })
                ->addColumn('action', function ($data) {
                    return view('templates.index_actions', [
                        'id' => $data->id, 'route' => $this->route,
                        'hideShow' => true ,'showModal' => false,
                    ])->render();
                })
                ->orderColumn('id', function ($query, $order) {
                     $query->orderBy('id', $order);
                })
                ->rawColumns(['checkbox', 'fields', 'action'])
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
    public function bulkStore(Request $request, Category $category)
    {
        $validated =  $request->validate([
            'title'     => 'required|array',
        ]);

        try {

            foreach($validated['title'] as $key => $title){

                $subcategory = new SubCategory();
                $subcategory->category_id = $category->id;
                $subcategory->title       = $title;

                $subcategory->save();
                
            }

            $data = [
                'status'   => true,
                'message'  => "Subcategoris added successfully.",
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
    public function show(Request $request, SubCategory $subcategory)
    {
        
    }

     /**
     * the form for editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SubCategory $subcategory)
    {
       
    }

    /**
     * Delete Category
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubCategory $subcategory)
    {   
        try {
            $subcategory->fields()->each(function($field){
                $field->delete();
            });

            $subcategory->delete();


            $data = [
                'status'   => true,
                'message'  => "Sub Category with it's fields Deleted!.",
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

                $subcategory = SubCategory::findOrfail($id);

                $subcategory->fields()->each(function($field){
                    $field->delete();
                });

                $subcategory->delete();

            }

            $data = [
                'status'   => true,
                'message'  => "Sub Category with it's fields Deleted!.",
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
