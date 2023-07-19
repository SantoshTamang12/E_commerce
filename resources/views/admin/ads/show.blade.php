@extends('templates.show')
@push('styles')
@endpush
@section('form_content')
    <div class="row">
        <div class="col-4 rounded">

            <div class="mt-2 text-left">

                <div class="text-left ">
                    <label class="text-left" for="title">Title</label>
                    <span class=" text-right ml-2">{{ $item->title ?: "N/A" }}</span>
                    <span class="ml-2 badge badge-{{ ($item->status === 'active' ? 'success' : ($item->status === 'inactive' ? 'secondary' : 'danger')) }}">{{ $item->status }}</span>
                </div>
                <div class="text-left align-items-center">
                    <label class="text-left" for="created">Created At</label>
                    <span class=" text-right ml-2">{{ $item->created_at->diffForHumans() ?: "N/A" }}</span>

                </div>
                <div class="text-left align-items-center">
                    <label class="text-left" for="adId">ID</label>
                    <span class=" text-right ml-2">{{ $item->adId ?: "N/A" }}</span>

                </div>
                <div class="text-left align-items-center">
                    <label class="text-left" for="is_featured">Featured</label>
                    <span class=" text-right ml-2 badge badge-{{ $item->is_featured ? 'success' : 'danger' }}">{{ $item->is_featured ? 'Yes' : "N/A" }}</span>

                </div>
                
            </div>

            <div class="mt-3 text-left">
                <label class="mb-1">Transaction -----</label>
                <div class="text-left align-items-center">
                    <label class="text-left" for="S">Sold</label>
                    <span class=" text-right ml-2 badge badge-{{ $item->sold ? 'success' : '' }}">{{ $item->sold ? 'Sold' : "N/A" }}</span>

                </div>
                <div class="text-left  d-flex flex-column mb-3">
                    <label class="text-left" for="seller">Seller ---</label>
                    <div class="">
                        <div class="mb-2">
                            <label class="text-left" for="name">Name</label>
                            <span class="text-right ml-2">
                                <a href="{{ route('users.show', $item->seller->id) }}">
                                    {{ $item->seller ? $item->seller->name  : 'N\A'}}
                                </a>
                            </span>
                        </div>
                        <div class="mb-2">
                            <label class="text-left" for="Phone"><i class="fa fa-phone mr-2"></i></label>
                            <span class="text-right ml-2">
                                
                                {{ $item->seller ? $item->seller->phone  : 'N\A'}}
                            </span>
                        </div>
                    </div>

                </div>
                <div class="text-left align-items-center">
                    <label class="text-left" for="buyer">Buyer</label>
                    <span class=" text-right ml-2">{{ $item->buyer ?  $item->buyer->name : 'N\A' }}</span>

                </div>

                <div class="text-left align-items-center">
                    <label class="text-left" for="price">Is Price</label>
                    <span class=" text-right ml-2 ">Rs. {{ $item->price ?: "N/A" }}</span>

                </div>
            </div>

        </div>
        <div class="col-8 rounded">
             <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active"   href="#about" data-toggle="tab">Info</a>
                    </li>
                    <li class="nav-item"><a class="nav-link "  href="#category"
                        data-toggle="tab">Category</a></li>
                    </li>
                    <li class="nav-item"><a class="nav-link "  href="#subcategory"
                        data-toggle="tab">Sub category</a></li>
                    </li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="about">
                        
                        <div class="border-bottom pt-2 d-flex justify-content-between w-100">
                            <label class="text-left" for="position">Position</label>
                            <div class="d-flex flex-column justify-content-end">
                                <span class=" text-right ">{{ !$item->position ? "N/A" : '' }}</span>
                                <span >Latitude : {{ $latitude }}</span>
                                <span>Longitude : {{ $longitude }}</span>
                            </div>

                        </div>
                        <div class="border-bottom pt-2 d-flex justify-between w-100">
                            <label class="text-left" for="latitude">Latitude</label>
                            <span class=" text-right ml-2">{{ $item->latitude ?: "N/A" }}</span>

                        </div>

                        <div class="border-bottom pt-2 d-flex justify-between w-100">
                            <label class="text-left" for="longitude">Longitude</label>
                            <span class=" text-right ml-2">{{ $item->longitude ?: "N/A" }}</span>

                        </div>
                        <div class="border-bottom pt-2 d-flex justify-between w-100">
                            <label class="text-left" for="description">Description</label>
                            <span class=" text-right ml-2 text-justify">{{ $item->description ?: "N/A" }}</span>

                        </div>

                    </div>
                    <div class=" tab-pane" id="category">
                        <div class="text-left ">
                            <label class="text-left" for="name">Name</label>
                            <span class=" text-right ml-2">
                                {{ $item->category->title }}
                            </span>
                        </div>
                  

                    </div>

                    <div class=" tab-pane" id="subcategory">
                        <div class="text-left ">
                            <label class="text-left" for="name">Name</label>
                            <span class=" text-right ml-2">
                                <a href="{{ route('categories.subcategories', $item->category->id) }}">{{ $item->subcategory->title }}</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>


@endsection

@push('scripts')
    <script>
        $(function () {

        });
    </script>
@endpush
