@extends('adminlte::page')
@section('css')
    @stack('styles')
@stop
@section('title', $title)
@section('content_header')
    <h1>View {{$title}}</h1>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col">
                    <!-- general form elements -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{$title}}</h3>
                            @if(!isset($hideEdit))
                                <a href="{{route($route.'edit', $item->id)}}" class="btn btn-primary float-right ml-3">
                                    <i class="fa fa-edit"></i>
                                    <span class="kt-hidden-mobile">Edit</span>
                                </a>
                            @endif
                            
                        </div>

                        <div class="card-body">
                            @yield('form_content')

                        </div>
                        <div class="card-footer">
                            <a href="javascript:history.back();" class="btn btn-default float-right">Cancel</a>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!--/.col (left) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@endsection

@section('js')
    @stack('scripts')
    <script>
        jQuery(document).ready(function () {
            $('#form input').attr('readonly', true);
            $('#form select').attr('disabled', true);
        });
    </script>
@stop
