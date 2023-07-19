<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Banner;
use App\Models\User;
use Yajra\DataTables\DataTables;

class BannerController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Banners';
        $this->resources = 'admin.banners.';
        parent::__construct();
        $this->route = 'banners.';
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

            $data = Banner::query()
                ->take(10);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', '<input type="checkbox" data-id="{{ $id }}" class="selected">'
                )
                 ->addColumn('image_url', function ($data) {
                    return $data->image_url;
                })
                ->addColumn('action', function ($data) {
                    return view('templates.index_actions', [
                        'id' => $data->id, 'route' => $this->route,
                        'hideShow' => true,
                        // 'hideEdit' => false,
                    ])->render();
                })
                ->orderColumn('id', function ($query, $order) {
                     $query->orderBy('id', $order);
                })
                ->rawColumns(['checkbox', 'image_url', 'action'])
                ->make(true);
        }

        $info['hideCreate'] = true;

        return view($this->indexResource(), $info);
    }



    /**
     * the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Store created resources in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function bulkStore(Request $request)
    {
         $validated =  $request->validate([
            'thumbnail'  => 'required|array',
            'title'     => 'required|array',
        ]);

        try {

            foreach($validated['title'] as $key => $title){

                $banner = new Banner();
                $banner->title       = $title;
                if($validated['thumbnail'][$key] != 'null'){
                    $banner->addMedia($validated['thumbnail'][$key])
                        ->toMediaCollection();
                }

                $banner->save();
                
            }

            $data = [
                'status'   => true,
                'message'  => "Banners added successfully.",
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
    public function show(Request $request, Banner $banner)
    {
        $info = $this->crudInfo();
        $info['item']        = $banner;
        $info['hideEdit']    = true;

        // dd($info);
        return view($this->showResource(), $info);
    }

     /**
     * the form for editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner)
    {
       
    }

    /**
     * Delete Category
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $banner)
    {   
        try {

            $banner->clearMediaCollection();
            $banner->delete();


            $data = [
                'status'   => true,
                'message'  => "Banner Deleted!.",
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

                $banner = Banner::findOrfail($id);

                $banner->clearMediaCollection();
                $banner->delete();

            }

            $data = [
                'status'   => true,
                'message'  => "Banner Deleted!.",
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
