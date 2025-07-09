@extends('admin.layouts.admin_master')

@section('title', 'Assignment List')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Assignment List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Phone Number</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>County</th>
                                <th>City</th>
                               
                                <th>Pincode</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTable will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Add necessary DataTables and SweetAlert2 CDN links -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
   $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url('admin/notify/users/data') }}',  // The URL for fetching the users data
            columns: [
                { data: 'first_name', name: 'first_name' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'country_name', name: 'country_name' },
                { data: 'state_name', name: 'state_name', render: function(data) {
                    return data === 'All' ? 'All' : data;
                }},
                { data: 'county_name', name: 'county_name', render: function(data) {
                    return data === 'All' ? 'All' : data;
                }},
                { data: 'city_name', name: 'city_name', render: function(data) {
                    return data === 'All' ? 'All' : data;
                }},
             
                { data: 'pincode', name: 'pincode', render: function(data) {
                    return data === 'All' ? 'All' : data;
                }},
                {
                    data: null, // no data for this column
                    name: 'actions', // name for this column
                    render: function(data, type, row) {
                        return `
                             <button class="btn btn-danger delete-btn" data-id="${row.area_id}">Delete</button>
                        `;
                    },
                    orderable: false, // Prevent sorting on this column
                    searchable: false // Prevent searching on this column
                }
            ]
        });

        // Edit button click event
        $(document).on('click', '.edit-btn', function() {
            var areaId = $(this).data('id');
            window.location.href = '{{ url('admin/notify/edit-user') }}/' + areaId;  // Redirect to the edit page
        });

        // Delete button click event
        $(document).on('click', '.delete-btn', function() {
            var areaId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('users.destroy') }}',  // The route to delete the user
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            areaId: areaId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'The user has been deleted.',
                                    'success'
                                );
                                table.ajax.reload();  // Reload the DataTable after deletion
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'There was an issue deleting the user.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'There was an issue deleting the user.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
