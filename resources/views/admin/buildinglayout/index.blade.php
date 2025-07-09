@extends('admin.layouts.admin_master')

@section('title', 'Building Layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Business Layout</h4>
                <div class="action-btn">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" id="openModalBtn"><i class="bi bi-plus-circle"></i></button>
                </div>
            </div>

            <!-- Create/Edit Building Layout Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Add New Business Layout</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createBuildingForm" action="{{ route('buildingLayout.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="layout_id" name="layout_id" value="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="building_name">Business Name:</label>
                                    <input type="text" id="building_name" name="building_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address">Address:</label>
                                    <input type="text" id="address" name="address" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="number_of_floors">Number of Floors:</label>
                                    <input type="number" id="number_of_floors" name="number_of_floors" class="form-control" min="1" required>
                                </div>
                                <div id="floorImagesContainer"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Layout</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table for existing building layouts -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-primary align-middle" id="allDataTable">
                        <thead class="table-light">
                            <tr>
                                <th>Sl No</th>
                                <th>Business Name</th>
                                <th>Address</th>
                                <th>Number of Floors</th>
                                <th>Floor Images</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="buildingLayoutTableBody">
                            @foreach($buildingLayouts as $index => $layout)
                                <tr id="layout-row-{{ $layout->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $layout->building_name }}</td>
                                    <td>{{ $layout->address }}</td>
                                    <td>{{ $layout->number_of_floors }}</td>
                                    <td>
                                        @foreach($layout->floorImages as $image)
                                            <img src="{{ asset('storage/' . $image->image_path) }}" width="50" height="50" alt="Floor Image {{ $image->floor_number }}">
                                        @endforeach
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm edit-btn" data-id="{{ $layout->id }}" data-bs-toggle="modal" data-bs-target="#createModal">Edit</button>
                                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $layout->id }}">Delete</button>
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
    // Show floor images inputs dynamically based on the number of floors
    $('#number_of_floors').on('change', function() {
        var numFloors = $(this).val();
        var container = $('#floorImagesContainer');
        container.empty(); // Clear any previous inputs

        // Add input fields for floor images
        for (var i = 1; i <= numFloors; i++) {
            container.append(
                `<div class="mb-3">
                    <label for="floor_images[${i}]">Floor ${i} Image:</label>
                    <input type="file" id="floor_image_${i}" name="floor_images[${i}]" accept="image/*" class="form-control">
                </div>`
            );
        }
    });

    // Open Edit Modal and Pre-fill Form
    $(document).on('click', '.edit-btn', function() {
        var layoutId = $(this).data('id');
        var url = "{{ route('buildingLayout.show', ':id') }}".replace(':id', layoutId);
        
        // Fetch data from the server using the ID to pre-fill the modal form
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var layout = response.layout;
                    $('#layout_id').val(layout.id);
                    $('#building_name').val(layout.building_name);
                    $('#address').val(layout.address);
                    $('#number_of_floors').val(layout.number_of_floors).trigger('change'); // Trigger the change event to load the floor images
                    // Populate the floor images (if any)
                    layout.floor_images.forEach(function(image, index) {
                        var floorImageInput = `
                            <div class="mb-3">
                                <label for="floor_images[${index + 1}]">Floor ${index + 1} Image:</label>
                                <input type="file" id="floor_image_${index + 1}" name="floor_images[${index + 1}]" accept="image/*" class="form-control">
                                <img src="{{ asset('storage/${image.image_path}') }}" width="50" height="50" alt="Floor Image ${index + 1}">
                            </div>
                        `;
                        $('#floorImagesContainer').append(floorImageInput);
                    });
                }
            },
            error: function(xhr, status, error) {
                alert('Error loading building layout data.');
            }
        });
    });

    // Handle Create/Update Layout form submission via AJAX
    $('#createBuildingForm').submit(function(e) {
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
                    // Create image HTML
                    let imageHtml = '';
                    response.layout.floor_images.forEach(function(image) {
                        imageHtml += `<img src="{{ asset('storage/${image.image_path}') }}" width="50" height="50" alt="Floor Image ${image.floor_number}">`;
                    });

                    // Create a new row with the data and images
                    var newRow = `
                        <tr id="layout-row-${response.layout.id}">
                            <td>${$('#buildingLayoutTableBody tr').length + 1}</td>
                            <td>${response.layout.building_name}</td>
                            <td>${response.layout.address}</td>
                            <td>${response.layout.number_of_floors}</td>
                            <td>${imageHtml}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="${response.layout.id}" data-bs-toggle="modal" data-bs-target="#createModal">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${response.layout.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                    $('#buildingLayoutTableBody').append(newRow);
                    $('#createModal').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                alert('Error saving the building layout. Please try again.');
                console.log(xhr.responseText);
            }
        });
    });

    // Handle delete via AJAX
    $(document).on('click', '.delete-btn', function() {
        var layoutId = $(this).data('id');
        var url = "{{ route('buildingLayout.destroy', ':id') }}".replace(':id', layoutId);
        if (confirm('Are you sure you want to delete this layout?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    if (response.success) {
                        $('#layout-row-' + layoutId).remove();
                    }
                }
            });
        }
    });
</script>
@endsection
