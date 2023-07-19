<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Field;
use App\Models\InAppAdd;
use App\Models\SubCategory;
use App\Models\User;

class DashboardController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $info['ads']                 = Ad::count() ;
        $info['banners']             = Banner::count() ;
        $info['categories']           = Category::count() ;
        $info['fields']              = Field::count() ;
        $info['inappads']           = InAppAdd::count() ;
        $info['subcategories']        = SubCategory::count() ;
        $info['users']               = User::count() ;


        return view('admin.dashboard', $info);
    }
}
