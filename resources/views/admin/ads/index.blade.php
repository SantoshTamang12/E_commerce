@extends('adminlte::page')


@section('title', $title)

@section('content_header')
    <h1>{{$title}} List</h1>
@stop

@section('css')
    <link href="{{ asset('custom/css/nepali-date-picker.css') }}" rel="stylesheet">
    <style>
        .andp-datepicker-container {
            z-index: 99999999999 !important;
        }
    </style>

@stop

@section('content')
    <div id="info" class="mx-2"></div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header row align-items-center">
                            <div class="col-3">
                                <h3 class="card-title">{{$title}} | Index</h3>
                            </div>

                            <div class="col-9 d-flex align-items-center justify-content-end">

                                <div id="bulkActions" 
                                    style="display: none;"
                                    class="  justify-content-end align-items-center">
                                    <span class="mx-2">Bulk Actions</span>
                                    
                                    
                                    <button type="button" id="deleteBulk" class="btn btn-sm btn-danger text-white rounded">
                                        <i
                                            class="fa fa-trash text-white"></i>
                                            <span class='ml-2'>Delete Selected</span>

                                    </button>

                                </div>

        
                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table" id="data-table">
                                <thead>
                                <tr class="text-left text-capitalize">
                                    <th><input type="checkbox" id="checkAll" name=""></th>
                                    <th>Seller</th>
                                    <th>Title</th>
                                    <th>Featured</th>
                                    <th>Sold</th>
                                    <th>status</th>
                                    <th>action</th>
                                </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>
@endsection

@section('js')
    <script>
        $(function () {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    type: 'id',
                    targets: 0
                  }],
                "order": [
                  [0, "desc"],
                ],
                ajax: "{{ route('ads.index') }}",
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable:false,searchable:false},
                    {data: 'user_id', name: 'user_id',orderable:false,searchable:false},
                    {data: 'title', name: 'title'},
                    {data: 'is_featured', name: 'is_featured'},
                    {data: 'sold', name: 'sold'},
                    {data: 'status', name: 'status', render: function (data, type, full, meta) {
                            switch (data) {
                                case ('active'):
                                    return `<span class="badge badge-success">Active</span>`;
                                    break;
                                case ('inactive'):
                                    return `<span class="badge badge-secondary">Inactive</span>`;
                                    break;
                                case ('expired'):
                                    return `<span class="badge badge-danger">Expired</span>`;
                                    break;
                                default:
                                    return `<span class="badge badge-secondary">Inactive</span>`;
                            }
                        }
                    },
                    {data: 'action', name: 'action'},
                ],
            });


            // Bulk Actions
            let IDS = [];

            // Multiple Deltee
            $('#data-table').on('click', '.selected[data-id]', function (e) {
                const ID = $(this).data('id');

                if (IDS.includes(ID)) {
                    var indexItem = IDS.indexOf(ID)
                    IDS.splice(indexItem, 1);

                } else {
                    IDS.push(ID);
                }

                if (IDS.length >= 1) {
                    $('#addAction').slideUp();
                    setTimeout(function () {
                        $('#bulkActions').slideDown();
                    }, 500)
                } else {
                    $('#addAction').slideDown();

                    $('#bulkActions').slideUp();
                }
                console.log(IDS);
            });

            // Check All
            $("#data-table").on('click', '#checkAll', function (e) {

                if (this.checked) {

                    $('#addAction').slideUp();

                    $('.selected[data-id]').each(function () {
                        $(this).prop("checked", true)

                        IDS.push($(this).data('id'));
                    });


                    $('#bulkActions').slideDown();


                } else {
                    $('#addAction').slideDown();

                    $('.selected[data-id]').each(function () {
                        $(this).prop("checked", false)
                    });

                    $('#bulkActions').slideUp();
                   
                }

            });

             // Remove Row
            $(document).on("click", '.removeRow', function(e){
                $(this).parent().parent().remove()

                // if(httpAction === 'create'){
                if($('#items').children().length < 1){

                    $('#addItemsDiv').slideUp();

                }  else {
                    $('#addItemsDiv').slideDown();
                }
                console.log($(this));


            })

            // Single Delete
            $('#data-table').on('click', '.btn-delete', function(e){
                e.preventDefault();

                if(!confirm('Are you sure?')){
                    return;
                }

                let id = $(this).data('id')
                let route = "{{ route('ads.destroy', '#ID') }}"
                setUpAjax();

                $.ajax({
                    url : route.replace('#ID', id),
                    method : "DELETE",
                    data : {
                        _method : "DELETE"
                    },
                    success: function(data){
                        if(data['status']){
                            $('#info').empty().append(`<div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                            </div>`)

                            refreshDatatable()

                        } else {
                            $('#info').empty().append(`<div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                            </div>`)
                        }
                    }, error: function(data){
                        showFailedMessage('Server Error.')
                    }
                })
            })

            // Delete in Bulksd
            $('#deleteBulk').click(function (e) {
                setUpAjax();

                //Confirm Delete
                if (!confirm('Are you sure?')) {
                    return;
                }

                // If Yes
                $.ajax({
                    url: "{{ route('ads.bulkDelete') }}",
                    type: 'POST',
                    data: {ids: IDS},
                    success: function (data) {
                        if (data['status']) {

                            // showSuccessMessage(data['message']);
                            $('#info').empty().append(`<div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                            </div>`)


                        } else {
                            $('#info').empty().append(`<div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                            </div>`)
                        }
                    }

                }).always(function (data) {
                   refreshDatatable();

                   IDS = [];

                    $('#bulkActions').slideUp();

                });
            })  

            // Refresh Datatable
            function refreshDatatable(){
                $('#data-table').DataTable().draw(false);
            }
        });
    </script>
@endsection
