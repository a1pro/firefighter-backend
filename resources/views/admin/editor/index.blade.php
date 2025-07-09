@extends('admin.layouts.admin_master')

@section('title', 'Editor Assignment')

@section('content')
<style>
    .select2-dropdown {
        z-index:99999;
    }
    span.select2-container {
        z-index:10050;
    }
    
    #city, #pincode {
        /* pointer-events: none; Make them read-only when "All" state is selected */
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Editor Users List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-primary align-middle" id="usersDataTable">
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
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm add-area-btn" data-user-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#addAreaModal">Assign Area</button>
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

<!-- Add Area Modal -->
<div id="addAreaModal" class="modal fade" tabindex="-1" aria-labelledby="addAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAreaModalLabel">Assign Area for User</h5>
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
                            <option value="all">All States</option> <!-- Added "All States" option -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="county">County:</label>
                        <select id="county" name="county[]" class="form-control" multiple required>
                            <option value="all">All Counties</option> <!-- Added "All Counties" option -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="city">City:</label>
                        <select id="city" name="city" class="form-control" required>
                            <option value="">Select City</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="pincode">Zipcode:</label>
                        <input type="text" id="pincode" name="pincode" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign Area</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
  $(document).ready(function() {
    // Open Add Area Modal and fetch country list
    $(document).on('click', '.add-area-btn', function() {
    let userId = $(this).data('user-id');
    $('#userIdForArea').val(userId);

    // Fetch countries from the backend
    $.ajax({
        url: "{{ route('editor.getCountries') }}", // Ensure this URL is correct
        type: "GET",
        success: function(response) {
            let countrySelect = $('#country');
            countrySelect.empty(); // Clear existing options
            countrySelect.append('<option value="">Select Country</option>'); // Add default placeholder option

            if (response.countries && response.countries.length > 0) {
                response.countries.forEach(function(country) {
                    countrySelect.append('<option value="' + country.id + '">' + country.name + '</option>');
                });

                // Re-initialize select2 after appending options
                countrySelect.select2({
                    placeholder: "Select Country",
                    allowClear: true,
                    width: "100%",
                    dropdownParent: $('#addAreaModal'),
                    matcher: function(params, data) {
                        // Match the country names that start with the typed letter
                        if ($.trim(params.term) === '') {
                            return data; // Return all results if the search term is empty
                        }

                        // Check if the country name starts with the entered term (case-insensitive)
                        if (data.text.toLowerCase().startsWith(params.term.toLowerCase())) {
                            return data; // Return the matching country
                        }

                        // Return null if no match is found
                        return null;
                    }
                });
            } else {
                console.log("No countries found");
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
});


    // Fetch states based on country selection
    $('#country').on('change', function() {
    let countryId = $(this).val();
    if (countryId) {
        $.ajax({
            url: '{{ route('editor.getStates', ':countryId') }}'.replace(':countryId', countryId),
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
                    dropdownParent: $('#addAreaModal'),
                    matcher: function(params, data) {
                        // Match the state names that start with the typed letter
                        if ($.trim(params.term) === '') {
                            return data; // Return all results if the search term is empty
                        }

                        // Check if the state name starts with the entered term (case-insensitive)
                        if (data.text.toLowerCase().startsWith(params.term.toLowerCase())) {
                            return data; // Return the matching state
                        }

                        // Return null if no match is found
                        return null;
                    }
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
        $('#county').empty().append('<option value="all">All Counties</option>');
        $('#city').empty().append('<option value="all">All Cities</option>');
        $('#pincode').val(''); // Clear pincode
        $('#county').prop('disabled', true); // Disable county
        $('#city').prop('disabled', true); // Disable city
        $('#pincode').prop('disabled', true); // Disable pincode
    } else {
        $('#county, #city, #pincode').prop('disabled', false); // Enable county, city, and pincode

        if (stateName) {
            $.ajax({
                url: '{{ route('editor.getCounty', ':stateName') }}'.replace(':stateName', stateName),
                type: "GET",
                success: function(response) {
                    let countySelect = $('#county');
                    countySelect.empty();
                    countySelect.append('<option value="">Select County</option>');
                    countySelect.append('<option value="all">All Counties</option>'); // Add "All Counties" option
                    response.counties.forEach(function(county) {
                        countySelect.append('<option value="' + county.id + '">' + county.county + '</option>');
                    });

                    // Re-initialize select2 for county dropdown with search functionality
                    $('#county').select2({
                        placeholder: "Select County",
                        allowClear: true,
                        width: "100%",
                        dropdownParent: $('#addAreaModal'),
                        matcher: function(params, data) {
                            // Match the county names that start with the typed letter
                            if ($.trim(params.term) === '') {
                                return data; // Return all results if the search term is empty
                            }

                            // Check if the county name starts with the entered term (case-insensitive)
                            if (data.text.toLowerCase().startsWith(params.term.toLowerCase())) {
                                return data; // Return the matching county
                            }

                            // Return null if no match is found
                            return null;
                        }
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

// Handle "All Counties" selection and apply search functionality
$('#county').on('change', function() {
    let selectedCounty = $(this).val();

    if (selectedCounty === 'all') {
        // If "All Counties" is selected, disable the dropdown and display message
        $(this).prop('disabled', true); // Disable county dropdown
        $(this).next().find('.select2-selection__rendered').text('All Counties Selected'); // Change displayed text to 'All Counties Selected'
    } else {
        $(this).prop('disabled', false); // Enable the county dropdown
    }
    
    // Reinitialize select2 for county dropdown after change, to maintain search
    $('#county').select2({
        placeholder: "Select County",
        allowClear: true,
        width: "100%",
        dropdownParent: $('#addAreaModal'),
        matcher: function(params, data) {
            // Match the county names that start with the typed letter
            if ($.trim(params.term) === '') {
                return data; // Return all results if the search term is empty
            }

            // Check if the county name starts with the entered term (case-insensitive)
            if (data.text.toLowerCase().startsWith(params.term.toLowerCase())) {
                return data; // Return the matching county
            }

            // Return null if no match is found
            return null;
        }
    });
});

$('#state').on('change', function() {
    let stateId = $(this).val();
    
    // If "All States" is selected, disable city and pincode
    if (stateId === 'all') {
        $('#city').empty().append('<option value="all">All Cities</option>');
        $('#pincode').val(''); // Clear pincode
        $('#city, #pincode').prop('disabled', true); // Disable city and pincode
    } else {
        // Enable city and pincode if a specific state is selected
        $('#city, #pincode').prop('disabled', false);
        $('#city').empty().append('<option value="all">All Cities</option>');
        
        if (stateId) {
            $.ajax({
                url: '{{ route('editor.getCities', ':stateId') }}'.replace(':stateId', stateId),
                type: "GET",
                success: function(response) {
                    let citySelect = $('#city');
                    citySelect.empty();
                    citySelect.append('<option value="">Select City</option>');
                    citySelect.append('<option value="all">All Cities</option>');
                    response.cities.forEach(function(city) {
                        citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                    });

                    // Re-initialize select2 for city dropdown with search functionality
                    $('#city').select2({
                        placeholder: "Select City",
                        allowClear: true,
                        width: "100%",
                        dropdownParent: $('#addAreaModal'),
                        matcher: function(params, data) {
                            // Match the city names that start with the typed letter
                            if ($.trim(params.term) === '') {
                                return data; // Return all results if the search term is empty
                            }

                            // Check if the city name starts with the entered term (case-insensitive)
                            if (data.text.toLowerCase().startsWith(params.term.toLowerCase())) {
                                return data; // Return the matching city
                            }

                            // Return null if no match is found
                            return null;
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching cities:', error);
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
        
        // If "All Counties" is selected, modify the county field value to "all"
        if ($('#county').val() === 'all') {
            formData = formData.replace(/county\%5B%5D=\%5Ball%5D/, 'county%5B%5D=all');
        }

        $.ajax({
            url: "{{ route('editor.storeArea') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert("Area successfully assigned.");
                    $('#addAreaModal').modal('hide');
                } else {
                    alert("Error assigning area.");
                }
            }
        });
    });
});

</script>
@endsection
