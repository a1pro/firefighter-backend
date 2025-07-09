@extends('admin.layouts.admin_master')

@section('title', 'Agency')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Agency List</h4>
                <div class="action-btn">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" id="openModalBtn"><i class="bi bi-plus-circle"></i></button>
                </div>
            </div>

            <!-- Create Agency Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Create New Agency</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createCategoryForm">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="agency_name">Agency Name:</label>
                                    <input type="text" id="createCategoryName" name="agency_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Create Agency</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Agency Modal -->
            <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Agency</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editCategoryForm">
                            @csrf
                            @method('POST')
                            <input type="hidden" id="editAgencyId" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="agency_name">Agency Name:</label>
                                    <input type="text" id="editAgencyName" name="agency_name" class="form-control" required>
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
                                <th>Agency Name</th>
                                <th>Total Users</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            @foreach($agency as $index => $icon)
                                <tr id="category-row-{{ $icon->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $icon->name }}</td>
                                    <td>{{agencyUserCount($icon->id)}}</td>
                                    <td>
                                        <button class="edit-category-btn btn btn-warning btn-sm" data-category-id="{{ $icon->id }}">Edit</button>
                                        <form action="{{ route('agency.destroy', $icon->id) }}" method="POST" class="delete-category-form" data-category-id="{{ $icon->id }}" style="display:inline-block;">
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


<script>
    // Open modal for creating new category
    $('#openModalBtn').click(function() {
        $('#createModal').modal('show');
    });

    // Handle create form submission via AJAX
   // Handle create form submission via AJAX
// Handle create form submission via AJAX
$('#createCategoryForm').submit(function(e) {
    e.preventDefault();

    var formData = $(this).serialize(); // Serialize form data

    $.ajax({
        url: "{{ route('agency.store') }}",
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                var newCategory = response.category;
                var newRow = '<tr id="category-row-' + newCategory.id + '">' +
                             '<td>' + ($('#categoriesTableBody tr').length + 1) + '</td>' +
                             '<td>' + newCategory.name + '</td>' +
                             '<td>0</td>' +  // Set user count to 0 for new agencies
                             '<td>' +
                             '<button class="edit-category-btn btn btn-warning btn-sm" data-category-id="' + newCategory.id + '">Edit</button>' +
                             '<form action="' + '{{ route('agency.destroy', ':id') }}'.replace(':id', newCategory.id) + '" method="POST" class="delete-category-form" data-category-id="' + newCategory.id + '" style="display:inline-block;">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm">Delete</button></form>' +
                             '</td>' +
                             '</tr>';
                $('#categoriesTableBody').append(newRow);
                
                // Close the modal and reset form
                $('#createModal').modal('hide'); // Close the modal
                $('#createCategoryForm')[0].reset();  // Reset form fields

                // Reset modal backdrop
                $('.modal-backdrop').remove();  // Remove the backdrop (opacity issue fix)
                
                // SweetAlert for success
                Swal.fire({
                    icon: 'success',
                    title: 'Agency Created!',
                    text: 'The new agency has been added successfully.',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function() {
            // SweetAlert for error
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'There was an error creating the agency. Please try again.',
                confirmButtonText: 'OK'
            });
        }
    });
});


    // Open modal for editing category
    $(document).on('click', '.edit-category-btn', function() {
        var id = $(this).data('category-id');

        $.ajax({
            url: "{{ route('agency.edit', ':id') }}".replace(':id', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var category = response.category;
                    $('#editAgencyName').val(category.name);  
                    $('#editAgencyId').val(category.id);
                    $('#editCategoryForm').attr('action', "{{ route('agency.update', ':id') }}".replace(':id', category.id));
                    $('#editModal').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was an error fetching the category data. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Handle edit form submission via AJAX
    $('#editCategoryForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var id = $('#editAgencyId').val();

        $.ajax({
            url: "{{ route('agency.update', ':id') }}".replace(':id', id),
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    var categoryRow = $('#category-row-' + response.category.id);
                    categoryRow.find('td:nth-child(2)').text(response.category.name); 
                    $('#editModal').modal('hide');

                    // SweetAlert for success
                    Swal.fire({
                        icon: 'success',
                        title: 'Agency Updated!',
                        text: 'The agency has been updated successfully.',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was an error updating the agency. Please try again.',
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
                            // SweetAlert for success
                            Swal.fire({
                                icon: 'success',
                                title: 'Agency Deleted!',
                                text: 'The agency has been deleted successfully.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        // SweetAlert for error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'There was an error deleting the agency. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });
</script>
@endsection
