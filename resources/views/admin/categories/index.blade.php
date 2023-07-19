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
    <form id="categoryAddForm" enctype="multipart/form-data">

        @csrf
        <section class="content mx-2 mb-3 pb-2 bg-white rounded">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="row  px-3 pt-3 align-items-center">
                            <div class="col-6 m-0 p-0">
                                <h5 class="">Add Categories</h5>
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

                                <div class="col-3">
                                    <label for="thumbnail">Thumbnail <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-8">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-1">

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

                                {{-- <div id="addAction" class=" justify-content-end align-items-center">
                                    <button type="button" id="add-item" class="btn btn-primary float-right"
                                            data-toggle="modal"
                                            data-target="#modal-form" data-whatever="@mdo">
                                        <i class="fa fa-plus"></i>Add
                                    </button>
                                </div> --}}
                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="data-table">
                                    <thead>
                                    <tr class="text-left text-capitalize">
                                        <th><input type="checkbox" id="checkAll" ></th>
                                        <th>Thumbnail</th>
                                        <th>Sub Category</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uuid/8.3.2/uuid.min.js" integrity="sha512-UNM1njAgOFUa74Z0bADwAq8gbTcqZC8Ej4xPSzpnh0l6KMevwvkBvbldF9uR++qKeJ+MOZHRjV1HZjoRvjDfNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(function () {

            listeners();

            let table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                columnDefs: [{
                    type: 'id',
                    targets: 0
                  }],
                "order": [
                  [0, "desc"],
                ],
                ajax: "{{ route('categories.index') }}",
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable:false, searchable:false},
                    {data: 'thumbnail', name: 'thumbnail', render: function(data, row){
                        if(data){
                            return `<img  src="${data}" class="data-zoomable w-50 h-25 rounded">`
                        }

                        let baseUrl = "{{ env('APP_URL') }}"

                        return `<img data-zoomable src="${baseUrl}/img/placeholder.jpg" class="data-zoomable w-50 h-25 rounded">`
                    }},
                    {data: 'sub_category', name: 'sub_category', orderable:false, searchable:false},
                    {data: 'title', name: 'title', orderable:true, searchable:true},
                    {data: 'action', name: 'action'},
                ],
            });


            function listeners(){


            }

            // Add New Row
            $("#add_new_row").click( async () => {
            
                addNewRow();         

                $('#addItemsDiv').slideDown()
            });

            let thumbnails = [];
            let index      = [];


            function addNewRow() {

                const categoryItem = $('#items');

                categoryItem.append(`
                    <div class="col-12 row mb-3 align-items-center">
                        <div class="col-3 position-relative" >
                            <button  class="btn btn-sm btn-danger position-absolute top-0 right-0 text-lg pointer text-white d-none removeImage">&times;</button>
                            <input type="file"  style="display:none;" name="thumbnail[]" class="fileInput form-control" >
                            <img  id="" class="img h-50 w-100 rounded "
                                src="/img/placeholder.jpg">

                        </div>
                        <div class="col-8 ">
                            <input type="text" name="title[]" class="title form-control" required>                         
                        </div>
                        
                        <div class="col-1">
                            <a  class="removeRow">
                                <span class="text-sm btn btn-danger btn-sm">&times;</span>
                            </a>
                        </div>

                    </div>
                `);


            }

            
            $('#categoryAddForm').on('change', '.fileInput', function(e){
                let image = $(this)[0].files[0] 
                thumbnails.push(image);

                console.log(image, $(this).next().attr('src'),  $(this).prev().slideUp())
                $(this).next().attr('src', `${URL.createObjectURL($(this)[0].files[0])}`)

                $(this).prev().removeClass('d-none');


            });


            // When click on image, click the file input
            $('#categoryAddForm').on('click', '.img', function(e){
                $(this).prev().click();

                console.log( $(this).prev())
            });


            // Remove Image & Set Default Image
            $('#categoryAddForm').on('click', '.removeImage', function(e){

                let imageUrl = "{{ env('APP_URL') }}"

                $(this).next().val('');

                $(this).next().next().attr('src', `${imageUrl}/img/placeholder.jpg`);

                console.log( $(this).next().next().attr('src'), imageUrl)

                $(this).addClass('d-none');


            });


            // Create Categories
            $('#categoryAddForm').submit(function(e){

                e.preventDefault();

                // let data = $(this).serialize();

                if($('#items').children().length < 1){
                    $('#categoryAddForm #addInfoText').addClass('animate__animated animate__bounce animate__delay-0.5s');

                    setTimeout(() => {
                        $('#categoryAddForm #addInfoText').removeClass('animate__animated animate__bounce animate__delay-0.5s')
                    }, 500)

                    return;
                }

                let formData = new FormData();
                const image = [...$('#categoryAddForm .fileInput')];

                $('#categoryAddForm .title').each(function(index, e){
                    formData.append('title[]', $(this).val());

                    if(!image[index].value){

                        formData.append(`thumbnail[${index}]`,  "null");

                    } else {

                        formData.append(`thumbnail[${index}]`, image[index].files[0] );
                    }

                });
          
                setUpAjax();


                // return;
                $.ajax({
                    type: 'POST',
                    url : "{{ route('categories.bulkStore') }}",
                    data : formData,
                    processData: false,
                    contentType: false ,               
                    success : function(data){


                        if(data['status']){

                            $("#items").html(``);

                            $("#addItemsDiv").slideUp();


                            $('#info').empty().append(`<div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                    ${data['message']}
                                </div>`)

                            removeMessage();
                        } else {
                            $('#info').empty().append(`<div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${data['message']}
                        </div>`)

                        }
                    },
                    error : function(err){

                        const response = JSON.parse(err.responseText);
                        let { thumbnail, title } = response.errors
                        if(thumbnail)
                        {
                            $('#info').empty().append(`<div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${thumbnail[0]}
                            </div>`)
                        }

                        if(title)
                        {
                            $('#info').empty().append(`<div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                ${title[0]}
                            </div>`)
                        }

                     
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
                let route = "{{ route('categories.destroy', '#ID') }}"
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
                        $('#info').empty().append(`<div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                Server Error!
                            </div>`)
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
                    url: "{{ route('categories.bulkDelete') }}",
                    type: 'POST',
                    data: {ids: IDS},
                    success: function (data) {
                        if (data['status']) {

                            // showSuccessMessage(data['message']);
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
