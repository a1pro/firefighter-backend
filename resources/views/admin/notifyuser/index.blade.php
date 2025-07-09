@extends('admin.layouts.admin_master')

@section('title', 'Notify Users')

@section('content')

<style>
.select2-dropdown {
    z-index:99999;
}
span.select2-container {
    z-index:10050;
}
    </style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title"> Notify User List</h4>
                <div class="action-btn">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" id="openModalBtn">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                </div>
            </div>

            <!-- Create Notify User Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Create New Notify User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createNotifyUserForm">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="first_name">First Name:</label>
                                    <input type="text" id="firstName" name="first_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="last_name">Last Name:</label>
                                    <input type="text" id="lastName" name="last_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone_number">Phone Number:</label>
                                    <input type="number" id="phoneNumber" name="phone_number" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Create Notify User</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Notify User Modal -->
            <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Notify User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editNotifyUserForm">
                            @csrf
                            @method('POST')
                            <input type="hidden" id="editUserId" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="first_name">First Name:</label>
                                    <input type="text" id="editFirstName" name="first_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="last_name">Last Name:</label>
                                    <input type="text" id="editLastName" name="last_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email">Email:</label>
                                    <input type="email" id="editEmail" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone_number">Phone Number:</label>
                                    <input type="number" id="editPhoneNumber" name="phone_number" class="form-control" required>
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



             <!-- Add Area Modal -->
             <div id="addAreaModal" class="modal fade" tabindex="-1" aria-labelledby="addAreaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addAreaModalLabel">Add Area for User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addAreaForm">
                            @csrf
                            <input type="hidden" id="userIdForArea" name="user_id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="country">Country:</label>
                                    <select id="country" name="country" class="form-control" required>
                                        <option value="">Select Country</option>
                                    </select>
                                </div>
                                <div class="mb-3">
    <label for="state">State:</label>
    <select id="state" name="state" class="form-control" required>
        <option value="">Select State</option>
        <option value="all">All States</option>
        <!-- Add "All States" option -->
    </select>
</div>
<div class="mb-3">
                        <label for="state">County:</label>
                        <select id="county" name="county" class="form-control" required>
                            <option value="">Select All County</option>
                            <option value="all">All Counties</option> <!-- Added "All States" option -->
                        </select>
                    </div>

<div class="mb-3">
    <label for="city">City:</label>
    <select id="city" name="city" class="form-control" required disabled> <!-- Disable initially -->
    <option value="">Select City</option>
        <option value="all">All States</option>
    </select>
</div>
 
<div class="mb-3">
    <label for="pincode">Pincode:</label>
    <input type="text" id="pincode" name="pincode" class="form-control" required disabled> <!-- Disable initially -->
</div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Area</button>
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
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            @foreach($users as $index => $notifyUser)
                                <tr id="user-row-{{ $notifyUser->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $notifyUser->first_name }}</td>
                                    <td>{{ $notifyUser->last_name }}</td>
                                    <td>{{ $notifyUser->email }}</td>
                                    <td>{{ $notifyUser->phone_number }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm edit-user-btn" data-user-id="{{ $notifyUser->id }}" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
                                        <form action="{{ route('notify-user.destroy', $notifyUser->id) }}" method="POST" class="d-inline delete-user-form" data-user-id="{{ $notifyUser->id }}">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                        <button class="btn btn-info btn-sm add-area-btn" data-user-id="{{ $notifyUser->id }}" data-bs-toggle="modal" data-bs-target="#addAreaModal">Add Area</button>
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
<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script> 
<script>
    // Handle create form submission via AJAX
    $('#createNotifyUserForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: "{{ route('notify-user.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    let newRow = `
                        <tr id="user-row-${response.notifyuser.id}">
                            <td>${$('#usersTableBody tr').length + 1}</td>
                            <td>${response.notifyuser.first_name}</td>
                            <td>${response.notifyuser.last_name}</td>
                            <td>${response.notifyuser.email}</td>
                            <td>${response.notifyuser.phone_number}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-user-btn" data-user-id="${response.notifyuser.id}" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
                                <form action="{{ route('notify-user.destroy', '') }}/${response.notifyuser.id}" method="POST" class="d-inline delete-user-form" data-user-id="${response.notifyuser.id}">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <button class="btn btn-info btn-sm add-area-btn" data-user-id="{{ $notifyUser->id }}" data-bs-toggle="modal" data-bs-target="#addAreaModal">Add Area</button>
                            </td>
                        </tr>
                    `;
                    $('#usersTableBody').append(newRow);
                    Swal.fire('Success', 'User created successfully', 'success');
                    $('#createModal').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                let response = xhr.responseJSON;
                if (response.errors.email) {
                    Swal.fire('Error', 'Email is already taken', 'error');
                } else if (response.errors.phone_number) {
                    Swal.fire('Error', 'Phone number is already taken', 'error');
                } else {
                    Swal.fire('Error', 'Failed to create user', 'error');
                }
            }
        });
    });

    // Populate the edit modal with user data
    $(document).on('click', '.edit-user-btn', function() {
        let userId = $(this).data('user-id');
        $.ajax({
            url: "{{ route('notify-user.edit', ':id') }}".replace(':id', userId),
            type: "GET",
            success: function(response) {
                if (response.notifyuser) {
                    $('#editUserId').val(response.notifyuser.id);
                    $('#editFirstName').val(response.notifyuser.first_name);
                    $('#editLastName').val(response.notifyuser.last_name);
                    $('#editEmail').val(response.notifyuser.email);
                    $('#editPhoneNumber').val(response.notifyuser.phone_number);
                } else {
                    Swal.fire('Error', 'Failed to fetch user data', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Failed to fetch user data', 'error');
            }
        });
    });

    // Handle edit form submission via AJAX
    $('#editNotifyUserForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        let userId = $('#editUserId').val();
        $.ajax({
            url: "{{ route('notify-user.update', ':id') }}".replace(':id', userId),
            type: "POST",
            data: formData,
            success: function(response) {
                Swal.fire('Success', 'User updated successfully', 'success').then(() => location.reload());
            },
            error: function(xhr, status, error) {
                let response = xhr.responseJSON;
                if (response.errors.email) {
                    Swal.fire('Error', 'Email is already taken', 'error');
                } else if (response.errors.phone_number) {
                    Swal.fire('Error', 'Phone number is already taken', 'error');
                } else {
                    Swal.fire('Error', 'Failed to update user', 'error');
                }
            }
        });
    });

    // Handle phone number input format validation
    $('#phoneNumber, #editPhoneNumber').on('input', function() {
        let phoneNumber = $(this).val();
        $(this).val(phoneNumber.replace(/\D/g, ''));
    });

    // Handle delete form submission via AJAX
    $(document).on('submit', '.delete-user-form', function(e) {
        e.preventDefault();
        let form = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', 'User has been deleted.', 'success').then(() => {
                                $('#user-row-' + form.data('user-id')).remove();
                            });
                        } else {
                            Swal.fire('Error', 'Failed to delete user', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Failed to delete user', 'error');
                    }
                });
            }
        });
    });
var $j = jQuery.noConflict();
$(document).ready(function() {
        // Open Add Area Modal and fetch country list
        $(document).on('click', '.add-area-btn', function() {
            let userId = $(this).data('user-id');
            $('#userIdForArea').val(userId);

            // Fetch countries from the backend
            $.ajax({
                url: "{{ route('countries.index') }}", // Ensure this URL is correct
                type: "GET",
                success: function(response) {
                    let countrySelect = $('#country');
                    countrySelect.empty(); // Clear existing options
                    countrySelect.append('<option value="">Select Country</option>'); // Add default placeholder option

                    // Ensure the response has countries
                    if (response.countries && response.countries.length > 0) {
                        response.countries.forEach(function(country) {
                            countrySelect.append('<option value="' + country.id + '">' + country.name + '</option>');
                        });

                        // Re-initialize select2 after appending options
                        $('#country').select2({
                            placeholder: "Select Country",
                            allowClear: true,
                            width: "100%", // Ensure dropdown takes the full width
                            dropdownParent: $('#addAreaModal')
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error); // Log any errors in the request
                }
            });
        });

        // Fetch states based on country selection
        $('#country').on('change', function() {
            let countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: '{{ route('states.index', ':countryId') }}'.replace(':countryId', countryId),
                    type: "GET",
                    success: function(response) {
                        let stateSelect = $('#state');
                        stateSelect.empty(); // Clear existing states
                        stateSelect.append('<option value="">Select State</option>'); // Default option
                        stateSelect.append('<option value="all">All States</option>'); // Add "All States" option
                        response.states.forEach(function(state) {
                            stateSelect.append('<option value="' + state.id + '">' + state.name + '</option>');
                        });

                        // Re-initialize select2 for state dropdown
                        $('#state').select2({
                            placeholder: "Select State",
                            allowClear: true,
                            width: "100%",
                            dropdownParent: $('#addAreaModal')
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching states:', error);
                    }
                });
            } else {
                $('#state').empty().append('<option value="">Select State</option>');
                $('#county').empty().append('<option value="">Select County</option>');
                $('#city').empty().append('<option value="">Select City</option>');
            }
        });

        // Fetch counties based on state selection
        $('#state').on('change', function() {
            let stateId = $(this).val();
            let stateName = $(this).find("option:selected").text();

            if (stateId === 'all') {
                // If "All States" is selected, disable county, city, and pincode
                $('#county').empty().append('<option value="">All Counties</option>');
                $('#city').empty().append('<option value="">All Cities</option>');
                $('#pincode').val(''); // Clear pincode
                $('#county, #city, #pincode').prop('disabled', true); // Disable county, city, and pincode
            } else {
                // Enable county, city, and pincode if a specific state is selected
                $('#county, #city, #pincode').prop('disabled', false);

                if (stateName) {
                    $.ajax({
                        url: '{{ route('counties.index', ':stateName') }}'.replace(':stateName', stateName),
                        type: "GET",
                        success: function(response) {
                            let countySelect = $('#county');
                            countySelect.empty();
                            countySelect.append('<option value="">Select County</option>');
                            response.counties.forEach(function(county) {
                                countySelect.append('<option value="' + county.id + '">' + county.county + '</option>');
                            });

                            // Re-initialize select2 for county dropdown
                            $('#county').select2({
                                placeholder: "Select County",
                                allowClear: true,
                                width: "100%",
                                dropdownParent: $('#addAreaModal')
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching counties:', error);
                        }
                    });
                } else {
                    $('#county').empty().append('<option value="">Select County</option>');
                }
            }
        });

        // Fetch cities based on state selection
        $('#state').on('change', function() {
            let stateId = $(this).val();

            // If "All States" is selected, disable city and pincode
            if (stateId === 'all') {
                $('#city').empty().append('<option value="">All Cities</option>');
                $('#pincode').val(''); // Clear pincode
                $('#city, #pincode').prop('disabled', true); // Disable city and pincode
            } else {
                // Enable city and pincode if a specific state is selected
                $('#city, #pincode').prop('disabled', false);

                if (stateId) {
                    $.ajax({
                        url: '{{ route('cities.index', ':stateId') }}'.replace(':stateId', stateId),
                        type: "GET",
                        success: function(response) {
                            let citySelect = $('#city');
                            citySelect.empty();
                            citySelect.append('<option value="">Select City</option>');
                            response.cities.forEach(function(city) {
                                citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                            });

                            // Re-initialize select2 for city dropdown
                            $('#city').select2({
                                placeholder: "Select City",
                                allowClear: true,
                                width: "100%",
                                dropdownParent: $('#addAreaModal')
                            });
                        }
                    });
                } else {
                    $('#city').empty().append('<option value="">Select City</option>');
                }
            }
        });

        // Submit the area assignment form
        $('#addAreaForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            $.ajax({
                url: '{{ route('user.area.store') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', 'Area assigned successfully', 'success');
                        $('#addAreaModal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to assign area', 'error');
                }
            });
        });
    });

</script>
@endsection
