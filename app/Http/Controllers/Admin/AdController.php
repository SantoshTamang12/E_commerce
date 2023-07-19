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
use App\Models\Ad;
use App\Models\User;
use Yajra\DataTables\DataTables;

class AdController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Ads';
        $this->resources = 'admin.ads.';
        parent::__construct();
        $this->route = 'ads.';
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

            $userId = request()->user_id;
            
            $data = Ad::query()
                ->when($userId, function($q) use ($userId) {
                    return $q->where('user_id', $userId);
                })
                ->take(10);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', '<input type="checkbox" data-id="{{ $id }}" class="selected">'
                )
                ->editColumn('user_id', function($data){
                    $user = User::find($data->user_id);

                    return '<a href="#?user='.$user->id.'">'.$user->name.'</a>';
                })
                ->editColumn('is_featured', function($data){
                    if($data->is_featured){
                        return '<span class="badge badge-success">Featured Rs.'.$data->price .'</span>';
                    }

                    return '<span class="badge badge-secondary">N</span>';
                })
                ->editColumn('sold', function($data){
                    if($data->sold){
                        return '<span class="badge badge-success">Sold</span>';
                    }

                    return '<span class="badge badge-danger">Not Sold</span>';
                })
                ->addColumn('action', function ($data) {
                    return view('templates.index_actions', [
                        'id' => $data->id, 'route' => $this->route,
                        'showModal' => false,
                    ])->render();
                })
                ->orderColumn('id', function ($query, $order) {
                     $query->orderBy('id', $order);
                })
                ->rawColumns(['checkbox', 'user_id', 'is_featured', 'sold', 'action'])
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
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Ad $ad)
    {
        $info = $this->crudInfo();
        $info['item']      = $ad;
        $position          = json_decode($ad->position, true);
        $info['latitude']  = $ad->latitude;
        $info['longitude']  = $ad->longitude;
        $info['seller']   = $ad->seller();
        $info['buyer']   = $ad->buyer();
        $info['category'] = $ad->category();
        $info['subcategory'] = $ad->subcategory();
        $info['hideEdit']    = true;

        // dd($info);
        return view($this->showResource(), $info);
    }

     /**
     * the form for editing a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Ad $ad)
    {
       
    }

    /**
     * Delete Category
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ad $ad)
    {   
        try {

            $ad->clearMediaCollection();
            $ad->delete();


            $data = [
                'status'   => true,
                'message'  => "Ad Deleted!.",
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

                $ad = Ad::findOrfail($id);

                $ad->clearMediaCollection();
                $ad->delete();

            }

            $data = [
                'status'   => true,
                'message'  => "Ads Deleted!.",
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
