@extends('adminlte::page')

@section('title','Profile')

@section('content_header')
    <h1>Profile</h1>
@stop

@section('css')
    <style type="text/css">
        .status{z-index: 99;}
    </style>
@stop


@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-block rounded mb-3 text-white">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
       </div>
    @endif

    @if(session('error'))
        <div class="mb-3 rounded alert alert-danger alert-block text-white">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">

                <!-- Profile Image -->
                <div class="card card-primary card-outline h-100">
                   
                    <div class="card-body box-profile position-relative">
                       
                        <div class="text-center">
                            <img class=" img-fluid rounded-circle"
                                 src="https://ui-avatars.com/api/?name={{ $admin->name }}"
                                 alt="User profile picture">
                        </div>
                        

                        <h3 class="profile-username text-left">{{$admin->name}}</h3>
                        <p class="text-muted text-left">{{$admin->email??'-'}}</p>
                    </div>
                    <!-- /.card-body -->
                </div>

            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link myLink" href="#about" data-toggle="tab">Info</a>
                            <li class="nav-item"><a class="nav-link myLink "  href="#password"
                                data-toggle="tab">Password</a></li>
                            </li>

                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="about">
                                <form id="infoForm" method="POST" 
                                    action="{{ route('profile.update', $admin->id) }}">
                                    @csrf
                                    @method('PATCH')
                                     <input type="hidden" name="type" value="info">
                                   

                                    <div class="container">
                                        <div class="d-flex justify-content-between items-center">
                                            <h4>Change Info</h4>
                                        
                                        </div>
                                        <div class="row my-2">
                                            <div class="col-12">
                                                <label for=""><span
                                                class="show-text">Name:</span></label>
                                                <br>
                                                <input type="text" name="name" class="form-control" value="{{ old('name') ?? $admin->name }}">
                                                @error('name')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror

                                            </div>
                                        </div>
                                       
                                       <div class="row my-2">
                                            <div class="col-12">
                                                <label for=""><span
                                                class="show-text">Email:</span></label>
                                                <br>
                                                <input type="text" name="email" class="form-control" value="{{ old('email') ?? $admin->email }}">
                                                @error('email')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror


                                            </div>
                                        </div>

                                   
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-end items-center">
                                        <button type="submit" class="btn btn-primary px-4">Save</button>
                                    </div>

                                </form>
                            </div>

                            <!-- /.tab-pane -->
                            <div class=" tab-pane @error('new_password') 'active' @enderror" id="password">
                                <form id="passwordForm" method="POST"
                                    action="{{ route('admin.password.update', $admin->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="type" value="password">
                                <div class="container">
                                    <div class="d-flex justify-content-between items-center">
                                        <h4>Change Password</h4>
                                    </div>
                                    <div class="row my-4">
                                        <div class="col-12">
                                            <label for=""><span
                                            class="show-text">Old Password:</span></label>
                                            <br>
                                            <input type="password" name="old_password" class="form-control">
                                            @error('old_password')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row my-4">
                                        <div class="col-12">
                                            <label for=""><span
                                            class="show-text">New Password:</span></label>
                                            <br>
                                            <input type="password" name="new_password" class="form-control">
                                            @error('new_password')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                        
                                    
                                <div class="d-flex justify-content-end items-center">
                                    <button type="submit" class="btn btn-primary px-5">Save</button>
                                </div>

                                </form>
                            </div>


                            <!-- /.tab-pane -->


                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
@endsection
@section('js')
   <script>
    let errors = JSON.parse(`<?php echo $errors; ?>`)

    if(errors.new_password){

        $('.tab-pane').first().removeClass('active')
        $('.tab-pane').last().addClass('active')

        $('.myLink').first().removeClass('active')
        $('.myLink').last().addClass('active')

        console.log(errors.new_password[0])
    } 

     // $('.myLink').first().click(function() {
     //   $('#info').addClass('active') 
     // })
     // if(!errors){
     //    // $('.nav-link').first().addClass('active')  
     // }
     console.log(errors)
   </script>
@endsection


