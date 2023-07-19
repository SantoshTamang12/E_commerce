<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Yajra\DataTables\DataTables;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Users';
        $this->resources = 'admin.users.';
        parent::__construct();
        $this->route = 'users.';
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
            $data = User::query()
                ->take(10);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', '<input type="checkbox" data-id="{{ $id }}" class="selected">
                    '
                )
                ->addColumn('action', function ($data) {
                    return view('templates.index_actions', [
                        'id' => $data->id, 'route' => $this->route,
                        'showModal' => false,
                    ])->render();
                })
                ->orderColumn('id', function ($query, $order) {
                     $query->orderBy('id', $order);
                })
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }

        $info['hideCreate'] = true;

        return view($this->indexResource(), $info);
    }



     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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
    public function show(Request $request,  User $user)
    {
        $info = $this->crudInfo();
        $info['item']        = $user;
        $info['ads']         = $user->ads();
        $info['hideEdit']    = true;
        // dd($info);
        return view($this->showResource(), $info);
    }


    /**
     * Delete user
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();


        return redirect()->back()->with(['success' => "User Deleted!"]);
    }

 
}
