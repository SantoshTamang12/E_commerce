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
    <div id="success" class="mx-2"></div>
    <form id="subcategoryAddForm" enctype="multipart/form-data">

        @csrf
        <section class="content mx-2 mb-3 pb-2 bg-white rounded">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="row  px-3 pt-3 align-items-center">
                            <div class="col-6 m-0 p-0">
                                <h5 class="">Add SubCategories</h5>
                            </div>
                            <div class="col-6 m-0 p-0 d-flex justify-content-end align-items-center">
                                <button id="add_new_row"  type="button"
                                    class="btn btn-primary btn-sm "
                                    >
                                    <i class="mr-2 fa fa-plus"></i>Add New Row
                                </button>
                                <button id="addSubmit" type="submit"
                                    class=" ml-3 btn btn-primary btn-sm "
                                    >
                                    <i class="mr-2 fa fa-paper-plane"></i>Submit
                                </button>
                            </div>
                        </div>

                        {{-- info about adding grades --}}
                        <div id="addInfo" class="row px-3 my-2 align-items-center">
                            <div id="addInfoText" class="bg-info rounded p-3 text-black col-12">
                                <i class="fa fa-exclamation-circle mr-2"></i>
                                <span class="mr-3">Please click on <b>+ Add New Row</b> to create a new row.</span>
                            </div>
                        </div>

                        {{-- Input rows --}}
                        <div class="row px-3 my-3 align-items-center">
                            <div class="col-12 row mb-1" id="addItemsDiv"
                                style="display: none;">
                                <div class="col-5">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-2">

                                </div>
                            </div>
                            <div id="items" class="col-12 row">

                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </section>

    </form>

    {{-- Fields Form --}}
    @include('admin.fields.form')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header row align-items-center">
                            <div class="col-3 align-items-center">
                                <h3 class="ml-2 card-title">{{$title}} | Index</h3>
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
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="data-table">
                                    <thead>
                                    <tr class="text-left text-capitalize">
                                        <th><input type="checkbox" id="checkAll" ></th>
                                        <th>Fields</th>
                                        <th>Title</th>
                                        <th>action</th>
                                    </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

@endsection

@section('js')
    <script>
        $(function () {

            let route      = "{{ route('categories.subcategories', '#ID') }}"
            let categoryId = "{{ $category }}"
            let selectedSubCategory;

            // let 
            listeners();

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
                ajax: route.replace('#ID', categoryId),
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable:false, searchable:false},
                    {data: 'fields', name: 'fields',orderable:false, searchable:false},
                    {data: 'title', name: 'title'},
                    {data: 'action', name: 'action'},
                ],
            });


            function listeners(){
               

            }
            // Add New Row
            $("#add_new_row").click( async () => {
                
                let parentDiv = $('#items');
                addNewRow(parentDiv, 'subcategory');         

                $('#addItemsDiv').slideDown()
            });


            $('#add_options_row').click(function(e){
                let parentDiv = $('#optionsItems');
                addNewRow(parentDiv, 'field');
            })

            function addNewRow(div, type) {

                if(type === 'field'){

                    div.append(`
                         <div class="col-12 align-items-center row mb-3">
                            <div class="col-10">
                                <input  placeholder="Options" id="options" name="start_date" class=" options form-control">
                            </div>
                            <div class="col-2">
                                <a  class="removeRow">
                                    <span class="text-sm btn btn-danger btn-sm">&times;</span>
                                </a>
                            </div>
                        </div>
                    `)


                    return;
                }

                div.append(`
                    <div class="col-12 row mb-3 align-items-center">
    
                        <div class="col-5 ">
                            <input type="text" name="title[]" class="title form-control" required>                         
                        </div>
                        
                        <div class="col-2">
                            <a  class="removeRow">
                                <span class="text-sm btn btn-danger btn-sm">&times;</span>
                            </a>
                        </div>

                    </div>
                `);


            }

        
            // Create Categories
            $('#subcategoryAddForm').submit(function(e){

                e.preventDefault();

                if($('#items').children().length < 1){
                    $('#subcategoryAddForm #addInfoText').addClass('animate__animated animate__bounce animate__delay-0.5s');

                    setTimeout(() => {
                        $('#subcategoryAddForm #addInfoText').removeClass('animate__animated animate__bounce animate__delay-0.5s')
                    }, 500)

                    return;
                }

                let formData = new FormData();


                $('#subcategoryAddForm .title').each(function(e, index){
                    formData.append('title[]', $(this).val());

                });
            
                let route  = "{{ route('subcategories.bulkStore',  '#ID') }}"

                setUpAjax();

                $.ajax({
                    type: 'POST',
                    url : route.replace('#ID', categoryId),
                    data : formData,
                    processData: false,
                    contentType: false ,               
                    success : function(data){


                        if(data['status']){

                            $("#items").html(``);

                            $("#addItemsDiv").slideUp();


                            $('#success').empty().append(`<div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                        </div>`)
                        } else {
                            $('#error').empty().append(`<div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                        </div>`)

                        }
                    },
                    error : function(err){

                        $('#error').empty().append(`<div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            Session or Grade is required to add classes. Please do refresh page if you have created them before.
                        </div>`)
                     
                    },
                }).always(function(data){
                    refreshDatatable();

                })
            })

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

                checkUncheckCheckbox(this.checked)

            });

            // Check or Uncheck 
            function checkUncheckCheckbox(checked){

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
            }

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
                let route = "{{ route('subcategories.destroy', '#ID') }}"
                setUpAjax();

                $.ajax({
                    url : route.replace('#ID', id),
                    method : "DELETE",
                    data : {
                        _method : "DELETE"
                    },
                    success: function(data){
                        if(data['status']){
                            showSuccessMessage(data['message'])

                            refreshDatatable()

                        } else {
                            showFailedMessage(data['message'])
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
                    url: "{{ route('subcategories.bulkDelete') }}",
                    type: 'POST',
                    data: {ids: IDS},
                    success: function (data) {
                        if (data['status']) {

                            // showSuccessMessage(data['message']);
                            $('#success').empty().append(`<div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                            </div>`)


                        } else {
                            showFailedMessage(data['message']);
                        }
                    }

                }).always(function (data) {
                   refreshDatatable();

                    IDS = [];

                    $('#bulkActions').slideUp();

                    $('.selected[data-id]').each(function () {
                        $(this).prop("checked", false)
                    });

                });
            })  

            // Refresh Datatable
            function refreshDatatable(){
                $('#data-table').DataTable().draw(false);
            }



            // Add Fields of Subcategor
            $('#data-table').on('click', '.add-field ', function(e){
                let id      = $(this).data('id')
                selectedSubCategory = id;
            })


            // Add Field forsubcategoru
            $('#create-submit').click(function(e){
                e.preventDefault();

                let route = "{{ route('fields.store', [ 'category' => '#CAT', 'subcategory' => '#SUB']) }}";
                route = route.replace('#CAT', categoryId);
                route = route.replace('#SUB', selectedSubCategory);


                let options = [];
                let label   = $('#field-modal .label').val()
                if (!label) {
                    $('#new-label').show();
                    return;
                } else {
                    $('#new-label').hide();                  
                }

                let type    = $('#field-modal .type').val()

                if (!type) {
                    $('#new-type').show();
                    return;
                } else
                {
                    $('#new-type').hide();

                }

                let is_price   = $('#field-modal .is_price').is(':checked') ? 1 : 0;
                let required   = $('#field-modal .required').is(':checked') ? 1 : 0;


                $('#field-modal .options').each(function(e){
                    options.push($(this).val());
                });


                console.log({
                        label : label,
                        type  : type,
                        is_price  : is_price,
                        required  : required,
                        options  : options,
                    }, route);

                // return;
                setUpAjax();

                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        label : label,
                        type  : type,
                        is_price  : is_price,
                        required  : required,
                        options  : JSON.stringify(options),
                    },
                    success: function (data) {
                        if (data['status']) {

                            $('#success').empty().append(`<div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                            </div>`)


                            $('#field-modal .close').click();
                        } else {
                            $('#error').empty().append(`<div class="alert alert-error alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                            </div>`)
                        }
                    }

                }).always(function (data) {
                   refreshDatatable();

                    IDS = [];

                    $('#bulkActions').slideUp();

                    $('.selected[data-id]').each(function () {
                        $(this).prop("checked", false)
                    });

                    selectedSubCategory = null;

                });

            })

        });

    </script>
@endsection
