@extends('admin.layouts.admin_master')

@section('title', 'Edit User')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Editor Details</h4>
            </div>
            <div class="card-body">
            <form id="editForm" method="POST">
    @csrf

    <!-- Hidden User ID -->
    <input type="hidden" id="area_id" name="area_id" value="{{ $user->area_id }}">

    <!-- Country Dropdown -->
    <div class="form-group">
        <label for="country_id">Country</label>
        <select name="country" id="country_id" class="form-control">
            <option value="">Select Country</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" 
                        {{ $user->country_id == $country->id ? 'selected' : '' }} >
                    {{ $country->short_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- State Dropdown -->
    <div class="form-group">
        <label for="state_id">State</label>
        <select name="state" id="state_id" class="form-control">
            <option value="">Select State</option>
            @foreach($states as $state)
                <option value="{{ $state->id }}" 
                        {{ $user->state_id == $state->id ? 'selected' : '' }}>
                    {{ $state->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- City Dropdown -->
    <div class="form-group">
        <label for="city_id">City</label>
        <select name="city" id="city_id" class="form-control">
            <option value="">Select City</option>
            @foreach($cities as $city)
                <option value="{{ $city->id }}" 
                        {{ $user->city_id == $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Area -->
    <div class="form-group">
        <label for="area">Area</label>
        <input type="text" id="area" name="area" class="form-control" value="{{ $user->area }}">
    </div>

    <!-- Pincode -->
    <div class="form-group">
        <label for="pincode">Zipcode</label>
        <input type="text" id="pincode" name="pincode" class="form-control" value="{{ $user->pincode }}">
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Update</button>
</form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // When a Country is selected, update States dropdown
    $(document).on('change', '#country_id', function() {
        var countryId = $(this).val();

        $.ajax({
            url: '{{ route("getStatesByCountry") }}',  // Correct route name for fetching states
            method: 'GET',
            data: { country_id: countryId },
            success: function(response) {
                $('#state_id').html('<option value="">Select State</option>'); // Clear state dropdown
                response.states.forEach(function(state) {
                    $('#state_id').append('<option value="' + state.id + '">' + state.name + '</option>');
                });
                
                // Optionally, reset city dropdown when country changes
                $('#city_id').html('<option value="">Select City</option>'); 
            }
        });
    });

    // When a State is selected, update Cities dropdown
    $(document).on('change', '#state_id', function() {
        var stateId = $(this).val();

        $.ajax({
            url: '{{ route("getCitiesByState") }}', // Correct route name for fetching cities
            method: 'GET',
            data: { state_id: stateId },
            success: function(response) {
                $('#city_id').html('<option value="">Select City</option>'); // Clear city dropdown
                response.cities.forEach(function(city) {
                    $('#city_id').append('<option value="' + city.id + '">' + city.name + '</option>');
                });
            }
        });
    });

    // Handle form submit to update the user assignment
    $(document).on('submit', '#editForm', function(e) {
        e.preventDefault();

        var userId = $('#area_id').val();  // Get the User ID from the hidden input
        var formData = $(this).serialize();  // Serialize the form data

        $.ajax({
            url: '{{ url("admin/update/area") }}/' + userId,  // Pass userId dynamically in URL
            method: 'POST',
            data: formData,
            success: function(response) {
                Swal.fire(
                    'Success!',
                    'The user assignment has been updated.',
                    'success'
                );
                // Optionally, reload page or redirect after success
                window.location.reload();
            },
            error: function(xhr) {
                Swal.fire(
                    'Error!',
                    'Something went wrong while updating the user.',
                    'error'
                );
            }
        });
    });
</script>
@endsection
