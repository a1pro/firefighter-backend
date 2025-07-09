@extends('admin.layouts.admin_master')

@section('title', 'Dashboard')

@section('content')

@if (session('info'))
<div class="alert alert-warning">
    <strong>{{ session('info') }}</strong>
</div>
@endif

<style>
    tr.odd {
        background: #eee;
    }
    table#users-table thead {
        color: #fff;
        background: #ed6d5c;
    }
    table#users-table th {
        padding:10px 0px;
        text-align: center;
    }
    div#users-table_length label, div#users-table_filter label {
        font-weight: 500;
    }
    table#users-table td {
        text-align: center;
        padding: 20px;
    }

    /* Style for the toggle switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 50px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 12px;
        width: 12px;
        border-radius: 50px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #4CAF50;
    }

    input:checked + .slider:before {
        transform: translateX(14px);
    }
</style>

<div class="row">
    <div class="col-12 col-lg-12 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-body">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-3 row-cols-xxl-3 g-3">
                    <div class="col">
                        <div class="card radius-10 bg-tiffany mb-0">
                            <div class="card-body text-center">
                                <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                                    <i class="bi bi-file-earmark-break-fill"></i>
                                </div>
                                <h3 class="text-white">{{ $total_users }}</h3>
                                <p class="mb-0 text-white">Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card radius-10 bg-danger mb-0">
                            <div class="card-body text-center">
                                <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                                    <i class="bi bi-hdd-fill"></i>
                                </div>
                                <h3 class="text-white">{{ $buildings }}</h3>
                                <p class="mb-0 text-white">Total Business</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card radius-10 bg-orange mb-0">
                            <div class="card-body text-center">
                                <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                                    <i class="bi bi-file-earmark-check-fill"></i>
                                </div>
                                <h3 class="text-white">{{ $icons }}</h3>
                                <p class="mb-0 text-white">Total Standard Icons</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <h3 class="text-center">User List</h3>
        
        <!-- DataTable -->
        <table class="table table-bordered" id="users-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Phone Number</th>
                    <th>Agency Name</th>
                    <th>Can Edit</th>
                    <th>Actions</th> <!-- Add actions column -->
                </tr>
            </thead>
            <tbody>
             
            </tbody>
        </table>
    </div>
</div><!--end row-->


{{-- ==================Edit Section Modal========================= --}}

<!-- Modal for Editing User -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="user_id">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="user_name" class="form-label">Username</label>
                        <input type="text" class="form-control" id="user_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="zipcode" class="form-label">Zipcode</label>
                        <input type="text" class="form-control" id="zipcode" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
 
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('getUsersData') !!}', // Make sure this route is correct
        columns: [
            { data: 'first_name', name: 'first_name' },
            { data: 'email', name: 'email' },
            { data: 'user_name', name: 'user_name' },
            { data: 'phone_number', name: 'phone_number' },
            {
                data: 'agency_name', 
                name: 'agency_name',
                render: function(data, type, row) {
                    var agencyColor = (data && data.trim() !== '') ? 'green' : 'red';
                    var agencyText = (data && data.trim() !== '') ? data : 'No Agency';
                    return `<span style="color: ${agencyColor};">${agencyText}</span>`;
                },
            },
            {
                data: 'role', 
                name: 'role',
                render: function(data, type, row) {
                    var checked = data === 'Editor' ? 'checked' : '';
                    return `
                        <label class="switch">
                            <input type="checkbox" ${checked} data-user-id="${row.id}" class="edit-toggle">
                            <span class="slider round"></span>
                        </label>
                    `;
                }
            },
            {
                data: 'id',
                name: 'id',
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-primary btn-sm edit-btn" data-id="${data}" data-bs-toggle="modal" data-bs-target="#userModal">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${data}">Delete</button>
                    `;
                }
            }
        ]
    });

    // Handle the toggle change event
    $('#users-table').on('change', '.edit-toggle', function() {
        var userId = $(this).data('user-id');
        var canEdit = $(this).prop('checked') ? 'Editor' : 'Viewer';

        $.ajax({
            url: '{{ route('update-can-edit') }}',
            method: 'POST',
            data: {
                user_id: userId,
                can_edit: canEdit,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                var statusMessage = canEdit;
                Swal.fire({
                    icon: 'success',
                    title: 'Role Updated',
                    text: `User role has been updated to ${statusMessage}`,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update user role!',
                    showConfirmButton: true
                });
            }
        });
    });

    // Handle edit button click
    $(document).ready(function() {
    // Open the edit modal and populate the form with user data
    $('#users-table').on('click', '.edit-btn', function() {
        var userId = $(this).data('id'); // Get the user ID from the button's data-id attribute

        // Send an AJAX request to fetch the user data
        $.ajax({
            url: '{{ url('admin/user') }}/' + userId,  // URL to fetch user details
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Populate the form fields with the user data
                    $('#user_id').val(response.user.id);
                    $('#first_name').val(response.user.first_name);
                    $('#last_name').val(response.user.last_name);
                    $('#email').val(response.user.email);
                    $('#user_name').val(response.user.user_name);
                    $('#zipcode').val(response.user.zipcode);

                    // Show the modal
                    $('#userModal').modal('show');
                } else {
                    // If the user data couldn't be fetched, show an error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                    });
                }
            },
            error: function(error) {
                // If there was an error during the AJAX request
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch user details.',
                });
            }
        });
    });
});


    // Handle form submission to save edits
    $(document).ready(function() {
    // Handle the form submission when 'Save changes' button is clicked
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var userId = $('#user_id').val(); // Get the user ID from the hidden input
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        var email = $('#email').val();
        var userName = $('#user_name').val();
        var zipcode = $('#zipcode').val();

        // Send AJAX request to update the user data
        $.ajax({
            url: '{{ route('admin.updateUser') }}',  // URL for the update route
            method: 'POST',
            data: {
                user_id: userId,
                first_name: firstName,
                last_name: lastName,
                email: email,
                user_name: userName,
                zipcode: zipcode,
                _token: '{{ csrf_token() }}', // Pass CSRF token
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    });

                    // Close the modal
                    $('#userModal').modal('hide');

                    // Optionally, you can refresh the DataTable to show the updated data
                    $('#users-table').DataTable().ajax.reload();
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                    });
                }
            },
            error: function(error) {
                // Handle any errors that occur during the AJAX request
                console.error('Error updating user data', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update user data.',
                });
            }
        });
    });
});

$(document).ready(function() {
    // Handle the delete button click
    $('#users-table').on('click', '.delete-btn', function() {
        var userId = $(this).data('id');  // Get the user ID from the data attribute

        // Show a confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request to delete the user
                $.ajax({
                    url: '{{ route('admin.deleteUser') }}',  // Correct route for deleting the user
                    method: 'POST',
                    data: {
                        user_id: userId,
                        _token: '{{ csrf_token() }}',  // CSRF token for security
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,  // Display the success message
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // Refresh the DataTable to reflect the deleted user
                            $('#users-table').DataTable().ajax.reload();
                        } else {
                            // Show error message if the user couldn't be deleted
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,  // Display the error message
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function(error) {
                        // Handle AJAX request errors
                        console.error('Error deleting user:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete user.',
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
    });
});
});

</script>
@endsection
