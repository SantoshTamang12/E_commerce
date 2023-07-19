@extends('templates.index')

@section('title', 'Users')

@section('content_header')
    <h1>Users</h1>


@stop

@section('ext_css')
@stop

@section('index_content')
    <div class="table-responsive">
        <table class="table" id="data-table">
            <thead>
                <tr class="text-left text-capitalize">
                    <th>name</th>
                    <th>email</th>
                    <th>phone</th>
                    <th>status</th>
                    <th>action</th>
                </tr>
            </thead>

        </table>
    </div>
@stop

@push('scripts')
    <script>
        $(function() {
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
                ajax: "{{ route('users.index') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, full, meta) {
                            switch (data) {
                                case ('active'):
                                    return `<span class="badge badge-success">Active</span>`;
                                    break;
                                case ('inactive'):
                                    return `<span class="badge badge-secondary">Inactive</span>`;
                                    break;
                                case ('document'):
                                    return `<span class="badge badge-primary">Document</span>`;
                                    break;
                                case ('onboarding'):
                                    return `<span class="badge badge-warning">Onboarding</span>`;
                                    break;
                                case ('balance'):
                                    return `<span class="badge badge-info">Balance</span>`;
                                    break;
                                case ('verification'):
                                    return `<span class="badge badge-light">Verification</span>`;
                                    break;
                                case ('banned'):
                                    return `<span class="badge badge-danger">Verification</span>`;
                                    break;
                                case ('suspended'):
                                    return `<span class="badge badge-dark">Verification</span>`;
                                    break;
                                default:
                                    return `<span class="badge badge-secondary">Inactive</span>`;
                            }
                        }
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ],
            });
        });
    </script>
@endpush
