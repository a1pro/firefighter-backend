@extends('admin.layouts.admin_master')

@section('title', 'Building Details')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Add Building Details</h4>
            </div>

            <!-- Building Form Modal -->
            <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Add New Building</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createBuildingForm" action="{{ route('buildings.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="building_name">Building Name:</label>
                                    <input type="text" id="building_name" name="building_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="building_address">Building Address:</label>
                                    <input type="text" id="building_address" name="building_address" class="form-control" readonly required>
                                </div>

                                <!-- Google Maps API for Address Selection -->
                                <div id="map" style="height: 300px;"></div>

                                <div class="mb-3">
                                    <label for="total_floor">Total Floors:</label>
                                    <input type="number" id="total_floor" name="total_floor" class="form-control" required>
                                </div>

                                <div id="floors-container"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Building</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    // Initialize Google Map API
    var map, marker, geocoder;
    function initMap() {
        geocoder = new google.maps.Geocoder();
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: -34.397, lng: 150.644 },
            zoom: 8
        });

        google.maps.event.addListener(map, 'click', function(event) {
            if (marker) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                position: event.latLng,
                map: map,
                title: "Selected Location"
            });

            geocoder.geocode({'location': event.latLng}, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        document.getElementById('building_address').value = results[0].formatted_address;
                    }
                }
            });
        });
    }

    // Add floor fields dynamically based on total floors
    $('#total_floor').on('change', function() {
        var totalFloors = $(this).val();
        var floorsContainer = $('#floors-container');
        floorsContainer.empty();

        for (var i = 1; i <= totalFloors; i++) {
            var floorHtml = `
                <div class="mb-3">
                    <label for="floor_name_${i}">Floor ${i} Name:</label>
                    <input type="text" id="floor_name_${i}" name="floor_name[${i}]" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="floor_image_${i}">Floor ${i} Image:</label>
                    <input type="file" id="floor_image_${i}" name="floor_image[${i}]" accept="image/*" class="form-control" required>
                </div>
            `;
            floorsContainer.append(floorHtml);
        }
    });

    // Handle the building form submission via AJAX
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
                    // Handle success (e.g., update the list of buildings or show a success message)
                    alert('Building added successfully!');
                    $('#createModal').modal('hide');
                }
            },
            error: function() {
                alert('Error adding the building. Please try again.');
            }
        });
    });

</script>

<!-- Include Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap&libraries=places&v=weekly" async></script>
@endsection
