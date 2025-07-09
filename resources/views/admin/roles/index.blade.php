@extends('admin.layouts.admin_master')

@section('title', 'Roles')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">List</h4>
                <div class="action-btn">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" id="openModalBtn"><i class="bi bi-plus-circle"></i> Add User</button>
                </div>
            </div>

            <!-- Create User Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createCategoryForm" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="role">Role:</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="">-- Select Role --</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Editor">Editor</option>
                                        <option value="Viewer">Viewer</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="name">Name:</label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password">Password:</label>
                                    
                     <input type="text" id="password" name="password" class="form-control" required>
        <!-- <span class="input-group-text bg-white">
            <i class="bi bi-eye toggle-password" style="cursor: pointer;" data-target="#password"></i>
        </span> -->

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Create User</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-primary align-middle" id="allDataTable">
                        <thead class="table-light">
                            <tr>
                                <th>Sl No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            @foreach($admin as $index => $user)
                            <tr id="category-row-{{ $user->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role }}</td>
                                <td>
                                    <form action="{{ route('admin-role.destroy', $user->id) }}" method="POST" class="delete-category-form" data-category-id="{{ $user->id }}" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize PerfectScrollbar only if the target element exists
        if ($('#allDataTable').length) {
            new PerfectScrollbar('#allDataTable');
        }

        // Handle create form submission via AJAX
        $('#createCategoryForm').submit(function (e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();
            let submitBtn = form.find('[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...');

            $.ajax({
                url: "{{ route('admin-role.store') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    submitBtn.prop('disabled', false).text('Create User');
                    
                    if (response.success) {
                        // Close modal and reset form
                        $('#createModal').modal('hide');
                        $('#createCategoryForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'User created successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload the page to show the new user
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Something went wrong.',
                        });
                    }
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).text('Create User');
                    
                    let errorText = "Validation failed!";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorText = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorText
                    });
                }
            });
        });

        // Handle delete user
        $(document).on('submit', '.delete-category-form', function (e) {
            e.preventDefault();
            let form = $(this);
            let userId = form.data('category-id');
            let deleteBtn = form.find('[type="submit"]');
            deleteBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action is irreversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'POST'
                        },
                        success: function (response) {
                            deleteBtn.prop('disabled', false).text('Delete');
                            if (response.success) {
                                $('#category-row-' + userId).remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'User has been deleted.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Optionally reload the page to refresh the numbering
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message || 'Could not delete user.', 'error');
                            }
                        },
                        error: function () {
                            deleteBtn.prop('disabled', false).text('Delete');
                            Swal.fire('Error!', 'Failed to delete user.', 'error');
                        }
                    });
                } else {
                    deleteBtn.prop('disabled', false).text('Delete');
                }
            });
        });
    });
</script>
<script>
// Password show/hide toggle
$(document).on('click', '.toggle-password', function () {
    const passwordInput = $('#password');
    const icon = $('#togglePasswordIcon');

    const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
    passwordInput.attr('type', type);

    // Toggle eye / eye-slash icon
    if (type === 'text') {
        icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
        icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
});

</script>
@endsection
