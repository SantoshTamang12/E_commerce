@extends('templates.show')
@push('styles')
@endpush
@section('form_content')
    <div class="row">
        <div class="col-4 rounded">

            <div class="mt-2 text-left">

                <div class="text-left ">
                    <label class="text-left" for="name">Name</label>
                    <span class=" text-right ml-2">{{ $item->name ?: "N/A" }}</span>
                    <span class="ml-2 badge badge-{{ ($item->status === 'active' ? 'success' : ($item->status === 'inactive' ? 'secondary' : 'danger')) }}">{{ $item->status }}</span>
                </div>
                <div class="text-left ">
                    <label class="text-left" for="email">email</label>
                    <span class=" text-right ml-2">{{ $item->email ?: "N/A" }}</span>
                </div>
                <div class="text-left ">
                    <label class="text-left" for="phone">Phone</label>
                    <span class=" text-right ml-2">{{ $item->phone ?: "N/A" }}</span>
                </div>
                <div class="text-left ">
                    <label class="text-left" for="gender">Gender</label>
                    <span class=" text-right ml-2">{{ $item->gender ?: "N/A" }}</span>
                </div>
                <div class="text-left align-items-center">
                    <label class="text-left" for="created">Created At</label>
                    <span class=" text-right ml-2">{{ $item->created_at->diffForHumans() ?: "N/A" }}</span>

                </div>

                
            </div>
            <div class="mt-3 text-left">

                <div class="text-left ">
                    <label class="text-left" for="otp">OTP</label>
                    <span class=" text-right ml-2">{{ $item->otp ?: "N/A" }}</span>
                </div>
                <div class="text-left ">
                    <label class="text-left" for="otp_verified_at">OTP Verified</label>
                    <span class=" text-right ml-2">{{ $item->otp_verified_at ? $item->otp_verified_at->diffForHumans() : "N/A" }}</span>
                </div>
                <div class="text-left ">
                    <label class="text-left" for="otp_sent_at">OTP Sent</label>
                    <span class=" text-right ml-2">{{ $item->otp_sent_at ? $item->otp_sent_at->diffForHumans() : "N/A" }}</span>
                </div>
            </div>
           
        </div>
        <div class="col-8 rounded">
             <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active"   href="#info" data-toggle="tab">Info</a>
                    </li>
                    <li class="nav-item"><a class="nav-link "  href="#ads"
                        data-toggle="tab">Ads</a></li>
                    </li>
                   
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="info">
                        
                        <div class="border-bottom pt-2 d-flex justify-between w-100">
                            <label class="text-left" for="location">Location</label>
                            <span class=" text-right ml-2">{{ $item->location ?: "N/A" }}</span>

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
                            <label class="text-left" for="dob">D.O.B</label>
                            <span class=" text-right ml-2 text-justify">{{ $item->dob ?: "N/A" }}</span>

                        </div>
                    </div>
                    <div class=" tab-pane" id="ads">
                        
                        <div class="table-responsive">
                            <table class="table" id="data-table">
                                <thead>
                                <tr class="text-left text-capitalize">
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
        </div>


@endsection

@push('scripts')
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
                ajax: "{{ url('/ads?user_id='. $item->id) }}",
                columns: [
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
        });
    </script>
@endpush
