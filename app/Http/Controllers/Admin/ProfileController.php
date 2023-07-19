<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseController
{
    public function __construct()
    {
        $this->title = 'Setting';
        $this->resources = 'admin.profile.';
        parent::__construct();
        $this->route = 'admin.profile';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $info = $this->crudInfo();

        $info['admin']  = auth()->user();

        return view('admin.profile.index', $info);
    }


    /**
     * Update Profile
     *
     * @return \Illuminate\Respnse
     */
    public function update(Request $request, $id)
    {

        $validated =  $request->validate([
            'name'  => 'required|string',
            'email'  => 'required|email|unique:admins,email',
        ]);

        try {
            auth()->user()->update([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
            ]);

            return back()->with(['success' => 'Profile updated.', 'type' => $request->type]);

        } catch (\Exception $e){
            return back()->with(['error' => $e->getMessage(), 'type' => $request->type]);
        }


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
    public function show(Request $request, $id)
    {
    
    }



    


    /**
     * Update Password
     *
     * @return \Illuminate\Respnse
     */
    public function updatePassword(Request $request,  $id)
    {   
        $user = auth()->user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'old_password' => 'required|string|min:5',
            'new_password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {

            return back()->withErrors($validator->messages());
        }

        try {
            if (!(Hash::check($request->get('old_password'), $user->getAuthPassword()))) {
                // The passwords matches
                return back()->withErrors(['new_password' => 'The old password doesnot match.', 'type' => 'password']);
            }

            //update
            if (strcmp($request->get('old_password'), $request->get('new_password')) == 0) {
      
                return back()->withErrors(['new_password' => 'New Password cannot be same as your current password. Please choose a different password.', 'type' => 'password']);
            }

            $user->update([
                'password'   => bcrypt($request->get('new_password')),
            ]);

            return back()->with(['success' => 'Password updated.']);

        } catch (\Exception $e){
            return back()->with(['error' => $e->getMessage()]);
        }


    }

 
}
