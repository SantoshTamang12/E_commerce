<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Field;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class FieldController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Fields';
        $this->resources = 'admin.fields.';
        parent::__construct();
        $this->route = 'fields.';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Category $category, SubCategory $subcategory)
    {
        $info = $this->crudInfo();

        $info['category']    = $category->id;
        $info['subcategory'] = $subcategory->id;

        if (request()->ajax()) {
            $data = Field::query()
                ->where('category_id', $category->id)
                ->where('sub_category_id', $subcategory->id)
                ->take(10);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', '<input type="checkbox" data-id="{{ $id }}" class="selected">'
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
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }



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
     * Store created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Category $category, SubCategory $subcategory)
    {
        $validated =  $request->validate([
            'label'     => 'required|unique:fields,label|string',
            'type'      => ['required', Rule::in(['SELECT', 'TEXT', 'TEXT_NUMBER'])],
            'options'   => 'nullable',
            'is_price'  => 'nullable|boolean',
            'required'  => 'nullable|boolean',
        ]);

        try {


            $field = new Field();
            $field->category_id     = $category->id;
            $field->sub_category_id = $subcategory->id;
            $field->label       = $validated['label'];
            $field->type        = $validated['type'];
            $field->is_price       = $validated['is_price'];
            $field->required       = $validated['required'];
            $field->options       = $validated['options'];
            $field->save();

            $data = [
                'status'   => true,
                'message'  => "Field added successfully.",
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
     * update  resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Field $field)
    {
        $validated =  $request->validate([
            'label'     => 'required|unique:fields,label|string',
            'type'      => ['required', Rule::in(['SELECT', 'TEXT', 'TEXT_NUMBER'])],
            'options'   => 'nullable',
            'is_price'  => 'nullable|boolean',
            'required'  => 'nullable|boolean',
        ]);

        try {

            $field->update([
                'label'       => $validated['label'],
                'type'        => $validated['type'],
                'options'     => $validated['options'],
                'is_price'    => $validated['is_price'],
                'required'    => $validated['required'],
            ]);

            $data = [
                'status'   => true,
                'message'  => "Field updated successfully.",
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
    public function show(Request $request, Field $field)
    {
        try {

            $data = [
                'status'   => true,
                'field'    => $field,
                'message'  => "Field updated successfully.",
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
    public function destroy(Field $field)
    {   
        try {
            $field->delete();


            $data = [
                'status'   => true,
                'message'  => "Field  Deleted!.",
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

                $field = Field::findOrfail($id);

                $field->delete();

            }

            $data = [
                'status'   => true,
                'message'  => "Fields Deleted!.",
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
