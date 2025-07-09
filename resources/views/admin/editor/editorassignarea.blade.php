@extends('admin.layouts.admin_master')

@section('title', 'Assignment List')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Editor Assignment List</h4>
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
                                <th>Zipcode</th>
                           
                                <th>Action</th>
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
        ajax: '{{ route('editor.getUsersData') }}',  // The URL for fetching the users data
        columns: [
            { data: 'first_name', name: 'first_name' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'country_name', name: 'country_name' },
            { data: 'state_name', name: 'state_name', render: function(data) {
                return data === 'All' ? 'All' : data;
            }},
            { data: 'county_names', name: 'county_names', render: function(data) {
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

    // Edit Button functionality
    $(document).on('click', '.edit-btn', function() {
        var userId = $(this).data('id');
        window.location.href = '{{ url('admin/edit-user') }}/' + userId;  // Redirect to the edit page
    });

    // Delete Button functionality
    $(document).on('click', '.delete-btn', function() {
        var recordId = $(this).data('id');
        
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
                    url: '{{ route('editor.delete') }}',  // The route to delete the EditorAssignArea entry
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: recordId  // Send the record ID for deletion
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'The record has been deleted.',
                                'success'
                            );
                            table.ajax.reload();  // Reload the DataTable
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'There was an issue deleting the record.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'There was an issue deleting the record.',
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
