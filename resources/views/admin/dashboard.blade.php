@extends('adminlte::page')

@section('content')
<div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{$ads}}</h3>
                    <p>Ads</p>
                </div>
                <div class="icon">
                    <i class="fas fa-biking"></i>
                </div>
                <a href="{{route('ads.index')}}" class="small-box-footer">More Info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{$users}}</h3>
                    <p>Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user"></i>
                </div>
                <a href="{{route('users.index')}}" class="small-box-footer">More Info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{$categories}}</h3>
                    <p>Categories</p>
                </div>
                <div class="icon">
                    <i class="fas fa-angle-double-right"></i>
                </div>
                <a href="{{route('categories.index')}}" class="small-box-footer">More Info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{$subcategories}}</h3>
                    <p>Sub Categories</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
                <a href="{{route('subcategories.index')}}" class="small-box-footer">More Info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-gradient-purple">
                <div class="inner">
                    <h3>{{$banners}}</h3>
                    <p>Banners</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-code"></i>
                </div>
                <a href="{{route('banners.index')}}" class="small-box-footer">More Info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-gradient-maroon">
                <div class="inner">
                    <h3>{{$inappads}}</h3>
                    <p>In App Ads</p>
                </div>
                <div class="icon">
                    <i class="fas fa-flag"></i>
                </div>
                <a href="{{route('inapp-ads.index')}}" class="small-box-footer">More Info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>


</div>
@endsection
