@extends('admin.layouts.admin_master')

@section('title', 'Standard Icons')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">List</h4>
                <div class="action-btn">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" id="openModalBtn"><i class="bi bi-plus-circle"></i></button>
                </div>
            </div>

            <!-- Create Category Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Create New Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createCategoryForm" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="category_name">Category Name:</label>
                                    <input type="text" id="createCategoryName" name="category_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category_image">Category Image:</label>
                                    <input type="file" id="createCategoryImage" name="category_image" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Create Category</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Category Modal -->
            <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editCategoryForm" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <input type="hidden" id="editCategoryId" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="category_name">Category Name:</label>
                                    <input type="text" id="editCategoryName" name="category_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category_image">Category Image:</label>
                                    <input type="file" id="editCategoryImage" name="category_image" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
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
                                <th>Category Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            @foreach($icons as $index => $icon)
                            @php
                            $icon->category_image = str_replace('public/', '', $icon->category_image);
                            
                            @endphp
                                <tr id="category-row-{{ $icon->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $icon->category_name }}</td>
                                    <td><img src="{{ asset('storage/' . $icon->category_image) }}" alt="Category Image" width="50" height="50" class="category-image"></td>
                                    <td>
                                        <button class="edit-category-btn btn btn-warning btn-sm" data-category-id="{{ $icon->id }}">Edit</button>
                                        <form action="{{ route('icon-categories.destroy', $icon->id) }}" method="POST" class="delete-category-form" data-category-id="{{ $icon->id }}" style="display:inline-block;">
                                            @csrf
                                            @method('POST')
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.js"></script>

<script>
    // Open modal for creating new category
    $('#openModalBtn').click(function() {
        $('#createModal').modal('show');
    });

    // Handle create form submission via AJAX
    $('#createCategoryForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('icon-categories.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var newCategory = response.category;
                    var newRow = '<tr id="category-row-' + newCategory.id + '">' +
                                 '<td>' + ($('#categoriesTableBody tr').length + 1) + '</td>' +
                                 '<td>' + newCategory.category_name + '</td>' +
                                 '<td><img src="' + removePublicPrefix(newCategory.image_url) + '" alt="Category Image" width="50" height="50"></td>' +
                                 '<td><button class="edit-category-btn btn btn-warning btn-sm" data-category-id="' + newCategory.id + '">Edit</button>' +
                                 '<form action="' + '{{ route('icon-categories.destroy', ':id') }}'.replace(':id', newCategory.id) + '" method="POST" class="delete-category-form" data-category-id="' + newCategory.id + '" style="display:inline-block;">@csrf @method('POST')<button type="submit" class="btn btn-danger btn-sm">Delete</button></form></td>' +
                                 '</tr>';
                    $('#categoriesTableBody').append(newRow);
                    $('#createModal').modal('hide');

                    // SweetAlert for success
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Created!',
                        text: 'The new category has been added successfully.',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                // SweetAlert for error
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was an error creating the category. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Handle edit button click to open the edit modal and populate the form
    $(document).on('click', '.edit-category-btn', function() {
        var categoryId = $(this).data('category-id');
        
        $.ajax({
            url: "{{ route('icon-categories.edit', ':id') }}".replace(':id', categoryId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var category = response.category;

                    $('#editCategoryId').val(category.id);
                    $('#editCategoryName').val(category.category_name);
                    $('#editModal').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was an error fetching the category details. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Handle edit form submission via AJAX (with file upload)
    $('#editCategoryForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var id = $('#editCategoryId').val();

        $.ajax({
            url: "{{ route('icon-categories.update', ':id') }}".replace(':id', id),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var categoryRow = $('#category-row-' + response.category.id);
                    categoryRow.find('td:nth-child(2)').text(response.category.category_name);
                    categoryRow.find('td:nth-child(3)').html('<img src="' + removePublicPrefix(response.category.image_url) + '" alt="Category Image" width="50" height="50">');
                    $('#editModal').modal('hide');

                    // SweetAlert for success
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Updated!',
                        text: 'The category has been updated successfully.',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was an error updating the category. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Handle delete category via AJAX
    $(document).on('submit', '.delete-category-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var categoryId = form.data('category-id');

        // SweetAlert for confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#category-row-' + categoryId).remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Category Deleted!',
                                text: 'The category has been deleted successfully.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'There was an error deleting the category. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    // Function to remove '/public' from the image URL
    function removePublicPrefix(imageUrl) {
        return imageUrl.replace('/storage', '/storage');
    }
</script>
@endsection
