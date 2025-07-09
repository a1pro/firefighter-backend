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

            <!-- Create Icon Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Upload New Icon</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createIconForm" action="{{ route('icons.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name">Icon Name:</label>
                                    <input type="text" id="createName" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="" disabled selected>Select Category</option>
                                        @if(isset($iconcategory) && !empty($iconcategory))
                                            @foreach($iconcategory as $ic)
                                                <option value="{{ $ic['id'] }}">{{ $ic['category_name'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No categories available</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="icon">Icon File:</label>
                                    <input type="file" id="createIcon" name="icon" accept="image/*" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Upload Icon</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Icon Modal -->
            <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Icon</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editIconForm" method="POST" enctype="multipart/form-data"> 
                            @csrf
                            @method('POST')
                            <input type="hidden" id="icon_id" name="id"> <!-- Hidden ID field -->
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name">Icon Name:</label>
                                    <input type="text" id="editName" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="icon">Icon File (Optional):</label>
                                    <input type="file" id="editIcon" name="icon" accept="image/*" class="form-control">
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
                                <th>Icon Name</th>
                                <th>Icon Category Name</th>
                                <th>Icon Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="iconsTableBody">
                            @foreach($icons as $index => $icon)
                                <tr id="icon-row-{{ $icon->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $icon->name }}</td>
                                    <td>{{ getIconCategory($icon->category_id) }}</td>
                              <td><img src="{{ asset(str_replace('public', 'storage', $icon->path)) }}" width="50" alt="Icon"></td>



 

                                    <td>
                                        <button class="edit-icon-btn btn btn-warning btn-sm" data-icon-id="{{ $icon->id }}">Edit</button>
                                        <form action="{{ route('icons.destroy', $icon->id) }}" method="POST" class="delete-icon-form" data-icon-id="{{ $icon->id }}" style="display:inline-block;">
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
    // Open modal for creating new icon
    $('#openModalBtn').click(function() {
        $('#createModal').modal('show');
    });

    // Handle create form submission via AJAX
    $('#createIconForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var newIcon = response.icon;
                    var newRow = '<tr id="icon-row-' + newIcon.id + '">' +
                                 '<td>' + ($('#iconsTableBody tr').length + 1) + '</td>' +
                                 '<td>' + newIcon.name + '</td>' +
                                 '<td>' + newIcon.category_name + '</td>' +
                                 '<td><img src="' + newIcon.path + '" width="50"></td>' +
                                 '<td><button class="edit-icon-btn btn btn-warning btn-sm" data-icon-id="' + newIcon.id + '">Edit</button>' +
                                 '<form action="/icons/' + newIcon.id + '" method="POST" class="delete-icon-form" data-icon-id="' + newIcon.id + '" style="display:inline-block;">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm">Delete</button></form></td>' +
                                 '</tr>';
                    $('#iconsTableBody').append(newRow);
                    $('#createModal').modal('hide');

                    // SweetAlert Success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Icon Added!',
                        text: 'The icon has been successfully uploaded.',
                    });
                }
            },
            error: function() {
                // SweetAlert Error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error uploading the icon. Please try again.',
                });
            }
        });
    });

    // Open modal for editing an icon
    $(document).on('click', '.edit-icon-btn', function() {
        var id = $(this).data('icon-id');
        
        var url = "{{ route('icons.edit', ':id') }}".replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var icon = response.icon;
                    $('#editName').val(icon.name);
                    $('#icon_id').val(icon.id);
                    $('#editIconForm').attr('action', '/icons/' + icon.id);
                    $('#editModal').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error fetching icon data. Please try again.',
                });
            }
        });
    });

    // Handle edit form submission via AJAX
    $('#editIconForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        var iconId = $('#icon_id').val();
        var url = "{{ route('icons.update', ':id') }}".replace(':id', iconId);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    var iconRow = $('#icon-row-' + response.icon.id);
                    iconRow.find('td:nth-child(2)').text(response.icon.name);
                    iconRow.find('img').attr('src', response.icon.path);

                    // SweetAlert Success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Icon Updated!',
                        text: 'The icon has been updated successfully.',
                    });

                    $('#editModal').modal('hide');
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error updating icon. Please try again.',
                });
            }
        });
    });

    // Handle delete icon via AJAX
    $(document).on('submit', '.delete-icon-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var iconId = form.data('icon-id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to delete this icon?',
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
                            $('#icon-row-' + iconId).remove();

                            // SweetAlert Success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Icon Deleted!',
                                text: response.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error deleting the icon. Please try again.',
                        });
                    }
                });
            }
        });
    });
</script>
@endsection
