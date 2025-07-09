@extends('admin.layouts.admin_master')  
@section('title', 'Buildings') 
 
 

 <style>

div#map {
    width: 100%;
}
    .select2-dropdown {
        z-index:99999;
    }
    span.select2-container {
        z-index:10050;
    }
   
 
   /* Toggle Switch CSS */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}
.status-pending {
    margin-top: 10px;
    display: inline-block;
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
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    border-radius: 50%;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: #4CAF50;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.status-approved {
    color: green;
}

.status-pending {
    color: orange;
}

table#buildingTable tr:nth-child(even) {
    box-shadow: none;
}
table#buildingTable tr:nth-child(odd) {
    box-shadow: none;
}
.table>:not(caption)>*>* {
    border:1px solid #d3d3d378;
    box-shadow: none!important;
    background-color: white!important;
}
/* Pending background color */
.status-pending {
    background-color: #ffc107; /* Yellow for pending */
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
}
.toggle-container {
    position: relative;
}

.toggle-checkbox {
    display: none;
}

.toggle-label {
    display: inline-block;
    width: 38px;
    height: 18px;
    background-color: #ccc;
    border-radius: 50px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.ak-upload {
    display: flex;
    align-items: center;
    gap: 8px;
}
.toggle-label::after {
    content: '';
    width: 14px;
    height: 14px;
    background-color: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: left 0.3s ease;
}

.toggle-checkbox:checked + .toggle-label {
    background-color: #4caf50;
}

.toggle-checkbox:checked + .toggle-label::after {
    left: 21px;
    
}


/* Custom CSS for Carousel Arrows */
.carousel-control-prev-icon,
.carousel-control-next-icon {
    height: 50px; /* Set the height of the circular button */
    width: 50px;  /* Set the width of the circular button */
    background-color: transparent; /* Set transparent background for the arrow icon */
    border-radius: 50%; /* Make the buttons circular */
    border: 1px solid black; /* Add a black border */
    outline: none; /* Remove outline */
    display: flex;
    align-items: center; /* Center the content vertically */
    justify-content: center; /* Center the content horizontally */
    font-size: 30px; /* Set the font size for the arrows */
}

.carousel-control-prev-icon {
    background-image: none; /* Remove default arrow image */
}
.carousel-control-next-icon, .carousel-control-prev-icon {

    background: black;
}
.carousel-control-next-icon, .carousel-control-prev-icon
{
    background-size: 60% 60%!important;
}

.carousel-control-next-icon {
    background-image: none; /* Remove default arrow image */
}


/* Ensure carousel button positioning */
.carousel-control-prev,
.carousel-control-next {
    z-index: 1; /* Ensure arrows are above carousel images */
}
button.btn {
    margin-top: 10px;
}
</style> 
<link rel="stylesheet" href="https://js.arcgis.com/4.25/esri/themes/light/main.css">

@section('content') 
<div class="row">     
    <div class="col-lg-12">         
        <div class="card">             
            <div class="card-header d-flex justify-content-between">                 
                <h4 class="card-title">Business List</h4>     
                @if(Auth::user()->role == "Super Admin")            
                <button class="btn btn-success" id="openCreateModalBtn" data-bs-toggle="modal" data-bs-target="#createBuildingModal">                     
                    <i class="bi bi-plus-circle"></i> Add Business                 
                </button>             
                @endif
            </div>             
            <div class="card-body">                 
                <div class="table-responsive">                     
                    <table class="table table-striped table-hover table-borderless table-primary align-middle" id="buildingTable">                         
                        <thead class="table-light">                             
                            <tr>                                 
                                <th>Sl No</th>                                 
                                                                 
                                <th>Address</th>    
                                <th>Suite/Unit Number</th>
                             
                                <th>Business Name</th>                            
                                <th>Total Floors</th>                              
                                <th>Floor</th> 
                               
                                <th>Basement</th>
                                <th>status</th>                                
                                <th>Action</th>                             
                            </tr>                         
                        </thead>                         
                        <tbody id="buildingsTableBody">
    @foreach($buildings as $index => $building)
        <tr id="building-row-{{ $building->id }}">
            <td>{{ $index + 1 }}</td>

            <td>{{ $building->building_address }}</td>
            <td>{{ $building->suite_number }}</td>
            <td>{{ $building->building_name }}</td>
            <td>{{ $building->total_floor }}</td>
 <!-- Floor Section -->
 <td>
    @if(count($building->floors) > 0)
        <p>{{ $building->floors[0]->floor_name }}</p>
        @php
            $imageData = $building->floors[0]->floor_image;
            $floorImages = json_decode($imageData, true);
        @endphp

        @if (is_array($floorImages) && isset($floorImages[0]))
            <img src="{{ asset('storage/' . $floorImages[0]) }}" alt="Floor Image" style="width: 100px; height: auto;">
        @elseif (is_string($imageData) && !empty($imageData))
            <img src="{{ asset('storage/' . $imageData) }}" alt="Floor Image" style="width: 100px; height: auto;">
        @else
            <p>No image available</p>
        @endif
    @else
        <p>No floors available</p>
    @endif
</td>

<!-- Basement Section -->
<td>
    @if(count($building->basements) > 0)
        @foreach($building->basements as $basement)
            <p>{{ $basement->basement_name ?? 'No basement name' }}</p>
            @php
                $imageData = $basement->basement_image;
                $basementImages = json_decode($imageData, true);
            @endphp

            @if (is_array($basementImages) && isset($basementImages[0]))
                <img src="{{ asset('storage/' . $basementImages[0]) }}" alt="Basement Image" style="width: 100px; height: auto;">
            @elseif (is_string($imageData) && !empty($imageData))
                <img src="{{ asset('storage/' . $imageData) }}" alt="Basement Image" style="width: 100px; height: auto;">
            @else
                <p>No image available</p>
            @endif
        @endforeach
    @else
        <p>No Basement available</p>
    @endif
</td>


            <td>
    <!-- Toggle Button -->
    <label class="switch">
        <input type="checkbox" class="status-toggle" data-id="{{ $building->id }}"
            {{ $building->status == 1 ? 'checked' : '' }}>
        <span class="slider round"></span>
    </label>
    <!-- Status Text -->
    <span id="status-text-{{ $building->id }}" class="ms-2 
        {{ $building->status == 1 ? 'status-approved' : 'status-pending' }}">
        {{ $building->status == 1 ? 'Approved' : 'Pending' }}
    </span>
</td>

<td>
    <!-- View Buttons -->
    <div class="mb-2">
        <button class="btn btn-info btn-sm viewAllFloorsBtn" 
                data-id="{{ $building->id }}" 
                data-floors="{{ json_encode($building->floors) }}" 
                data-bs-toggle="modal" 
                data-bs-target="#viewAllFloorsModal">
            View Floors
        </button>

        <button class="btn btn-info btn-sm viewAllBasementBtn" 
                data-id="{{ $building->id }}" 
                data-basement="{{ json_encode($building->basements) }}" 
                data-bs-toggle="modal" 
                data-bs-target="#viewAllBasementModal">
            View Basement
        </button>
    </div>

    <!-- Edit & Gallery Buttons -->
    <div class="mb-2">
        <button class="btn btn-warning btn-sm editBuildingBtn" 
                data-id="{{ $building->id }}" 
                data-building_name="{{ $building->building_name }}" 
                data-building_address="{{ $building->building_address }}" 
                data-suite_number="{{ $building->suite_number }}" 
                data-total_floor="{{ $building->total_floor }}" 
                data-floors="{{ json_encode($building->floors) }}" 
                data-bs-toggle="modal" 
                data-bs-target="#editBuildingModal">
            Edit
        </button>

        <button class="btn btn-primary btn-sm galleryBtn" 
                data-id="{{ $building->id }}" 
                data-floors="{{ json_encode($building->floors) }}" 
                data-basement="{{ json_encode($building->basements) }}" 
                data-bs-toggle="modal" 
                data-bs-target="#galleryModal">
            Gallery
        </button>
    </div>

    <!-- Delete Form -->
    <form action="{{ route('admin.buildings.destroy', $building->id) }}" 
          method="POST" 
          class="d-inline deleteForm">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm deleteButton">
            Delete
        </button>
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

<!-- View All Floors Modal -->
<div id="viewAllFloorsModal" class="modal fade" tabindex="-1" aria-labelledby="viewAllFloorsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAllFloorsModalLabel">All Floors</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Floor Name</th>
                            <th>Floor Image</th>
                        </tr>
                    </thead>
                    <tbody id="viewAllFloorsModalBody">
                        <!-- Floors will be populated here dynamically -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!---View All Basement -->
<div id="viewAllBasementModal" class="modal fade" tabindex="-1" aria-labelledby="viewAllBasementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAllBasementModalLabel">All Basement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Basement Name</th>
                            <th>Basement Image</th>
                        </tr>
                    </thead>
                    <tbody id="viewAllBasementModalBody">
                        <!-- Floors will be populated here dynamically -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Basement Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="imagePreview" src="" alt="Image Preview" class="img-fluid" />
            </div>
        </div>
    </div>
</div>

<!-- Create Building Modal -->
<div id="createBuildingModal" class="modal fade" tabindex="-1" aria-labelledby="createBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBuildingModalLabel">Add New Business</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createBuildingForm" action="{{ route('admin.buildings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Business Address -->
                    <div class="mb-3">
            <label for="building_address">Business Address</label>
            <input type="text" name="building_address" id="building_address" class="form-control" required>
        </div>

        <!-- Map Location -->
        <div id="mapContainer" class="mb-3">
            <label for="building_address">Locate Business on Map</label>
            <div id="map" style="height: 300px;width:100%!important;"></div>
        </div>
        <label for="viewSwitch">Select View:</label>
<select id="viewSwitch">
  <option value="street">Street View</option>
  <option value="satellite">Satellite View</option>
  <option value="aerial">Aerial View</option>
</select>

                        <!-- Suite / Unit Number -->
                        <div class="mb-3">
                            <label for="suite_number">Suite / Unit Number</label>
                            <input type="text" name="suite_number" id="suite_number" class="form-control">
                        </div>

                    <!-- Business Name -->
                    <div class="mb-3">
                        <label for="building_name">Business Name</label>
                        <input type="text" name="building_name" id="building_name" class="form-control" required>
                    </div>

                    

                   <div class="mb-3">                         
                        <label for="zipcode">Zip Code</label>                         
                        <input type="text" name="zipcode" id="zipcode" class="form-control">                     
                    </div>  
                    
                    <!-- Total Floors Input -->
                   <!-- <div class="mb-3">
                        <label for="total_floor">Total Floors</label>
                        <input type="number" name="total_floor" id="total_floor" class="form-control" min="1">
                    </div>-->

                    <!-- Add Floor Detail Button -->
                    <div id="addFloorButtonContainer" class="mb-3"></div>

                    <!-- Floor Fields -->
                    <div id="floorFields"></div>

                    <!-- Total Basements Input -->
                   <!-- <div class="mb-3">
    <label for="total_basement">Total Basements</label>
    <input type="number" name="total_basement" id="total_basement" class="form-control" min="0">
</div>-->

<!-- Add Basement Detail Button -->
<div id="addBasementButtonContainer" class="mb-3"></div>

<!-- Basement Fields (if any) -->
<div id="basementFields"></div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Business</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<!-- Edit Building Modal -->
<div id="editBuildingModal" class="modal fade" tabindex="-1" aria-labelledby="editBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBuildingModalLabel">Edit Business</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBuildingForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Business Address -->
                    <div class="mb-3">
                        <label for="edit_building_address">Business Address</label>
                        <input type="text" name="building_address" id="edit_building_address" class="form-control" required>
                    </div>

                    <!-- Map Location -->
                    <div id="editMapContainer" class="mb-3">
                        <label>Locate Business on Map</label>
                        <div id="editMap" style="height: 300px;width:100%!important;"></div>
                    </div>
                    <label for="editViewSwitch">Select View:</label>
                    <select id="editViewSwitch">
                        <option value="street">Street View</option>
                        <option value="satellite">Satellite View</option>
                        <option value="aerial">Aerial View</option>
                    </select>

                    <!-- Suite / Unit Number -->
                    <div class="mb-3">
                        <label for="edit_suite_number">Suite / Unit Number</label>
                        <input type="text" name="suite_number" id="edit_suite_number" class="form-control">
                    </div>

                    <!-- Business Name -->
                    <div class="mb-3">
                        <label for="edit_building_name">Business Name</label>
                        <input type="text" name="building_name" id="edit_building_name" class="form-control" required>
                    </div>

                    <!-- Zip Code -->
                    <div class="mb-3">                         
                        <label for="edit_zipcode">Zip Code</label>                         
                        <input type="text" name="zipcode" id="edit_zipcode" class="form-control">                     
                    </div>  
                    
                    <!-- Floor Fields -->
                    <div class="mb-3">
                        <h5>Floors</h5>
                        <div id="editFloorFields"></div>
                        <div id="editAddFloorButtonContainer" class="mb-3"></div>
                    </div>

                    <!-- Basement Fields -->
                    <div class="mb-3">
                        <h5>Basements</h5>
                        <div id="editBasementFields"></div>
                        <div id="editAddBasementButtonContainer" class="mb-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Business</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>




<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="galleryModalLabel">Building Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="galleryModalBody">
                <!-- Gallery content will be inserted here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for displaying the full-screen image -->
<div id="fullScreenImageModal" class="modal fade" tabindex="-1" aria-labelledby="fullScreenImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullScreenImageModalLabel">Floor Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fullScreenImage" src="" alt="Full Screen Floor Image" style="width: 100%; max-height: 80vh; object-fit: contain;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>





@endsection  

@section('script') 
<!-- Mapbox Geocoder CSS -->
<script src="https://js.arcgis.com/4.24/"></script>

 


    <!-- Include the required Select2 CSS -->
   

 
 <script>
    // Function to handle the delete confirmation with SweetAlert
    $(document).ready(function () {

// Handle delete action with SweetAlert
$('.delete-btn').on('click', function(e) {
    e.preventDefault();
    const deleteForm = $(this).closest('form');
    
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form to delete the building
            deleteForm.submit();
            Swal.fire(
                'Deleted!',
                'The building has been deleted.',
                'success'
            );
        } else {
            Swal.fire(
                'Cancelled',
                'The building is safe.',
                'info'
            );
        }
    });
});

// Toggle building status (pending/approved) with SweetAlert confirmation


});

 
</script>
<script>
    // Global variables for map functionality
    let selectedLat = null;
    let selectedLng = null;
    let currentMarker = null;
    let mapView = null;
    let searchWidget = null;
    let sceneView = null;
    const clientId = 'REN3uYeuFQVJdGjy';
    const clientSecret = '12d1260890984309971e0fe4ae504103';

    // =============================================
    // COMMON MAP FUNCTIONS (USED BY BOTH CREATE AND EDIT)
    // =============================================

    // Function to get OAuth token
    async function fetchToken(clientId, clientSecret) {
        const url = 'https://www.arcgis.com/sharing/rest/oauth2/token';
        const params = new URLSearchParams();
        params.append('client_id', clientId);
        params.append('client_secret', clientSecret);
        params.append('grant_type', 'client_credentials');

        const response = await fetch(url, {
            method: 'POST',
            body: params
        });
        const data = await response.json();
        if (data.access_token) {
            return data.access_token;
        } else {
            throw new Error("Failed to retrieve access token");
        }
    }

    // Initialize ESRI map with token
    function initializeMapWithToken(mapElement, token, preselectedLat, preselectedLng, input) {
        require([
            "esri/Map", 
            "esri/views/SceneView",
            "esri/widgets/Search", 
            "esri/Graphic", 
            "esri/geometry/Point", 
            "esri/symbols/SimpleMarkerSymbol", 
            "esri/layers/SceneLayer", 
            "esri/layers/TileLayer", 
            "esri/layers/VectorTileLayer",
            "esri/PopupTemplate"
        ], function (Map, SceneView, Search, Graphic, Point, SimpleMarkerSymbol, SceneLayer, TileLayer, VectorTileLayer, PopupTemplate) {

            // Create base map layers
            const streetLayer = new TileLayer({
                url: "https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer"
            });

            const satelliteLayer = new TileLayer({
                url: "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer"
            });

            const aerialLayer = new TileLayer({
                url: "https://server.arcgisonline.com/ArcGIS/rest/services/World_Aerial/MapServer"
            });

            // Create the base map
            const map = new Map({
                basemap: "streets-navigation-vector",
                ground: "world-elevation"
            });

            // Use SceneView for 3D representation
            sceneView = new SceneView({
                container: mapElement,
                map: map,
                center: [preselectedLng || -118.80500, preselectedLat || 34.02700],
                zoom: 15,
                camera: {
                    position: [preselectedLng || -118.80500, preselectedLat || 34.02700, 100],
                    heading: 0,
                    tilt: 45
                },
                ui: {
                    components: ["search", "attribution"]
                }
            });

            // Add search widget
            searchWidget = new Search({
                view: sceneView
            });
            sceneView.ui.add(searchWidget, "top-right");

            // Function to add a marker to the map
            function addMarker(latitude, longitude) {
                if (currentMarker) {
                    sceneView.graphics.remove(currentMarker);
                }

                const point = new Point({
                    latitude: latitude,
                    longitude: longitude
                });

                currentMarker = new Graphic({
                    geometry: point,
                    symbol: new SimpleMarkerSymbol({
                        color: [255, 0, 0],
                        size: "12px",
                        outline: {
                            color: [255, 255, 255],
                            width: 3
                        }
                    }),
                    popupTemplate: new PopupTemplate({
                        title: "Building Information",
                        content: `<p>Latitude: {latitude}</p><p>Longitude: {longitude}</p>`
                    })
                });

                sceneView.graphics.add(currentMarker);
                reverseGeocode(latitude, longitude, token, input);
            }

            // Function to reverse geocode
            function reverseGeocode(latitude, longitude, token, input) {
                const url = `https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/reverseGeocode?f=json&location=${longitude},${latitude}&outSR=4326&token=${token}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.address) {
                            input.value = data.address.Match_addr;
                        } else {
                            input.value = "Address not found.";
                        }
                    })
                    .catch(error => console.error("Error in reverse geocoding:", error));
            }

            // Handle map click event
            sceneView.on("click", function (event) {
                const latitude = event.mapPoint.latitude;
                const longitude = event.mapPoint.longitude;
                addMarker(latitude, longitude);
                selectedLat = latitude;
                selectedLng = longitude;
                input.value = `Latitude: ${latitude}, Longitude: ${longitude}`;
            });

            // Handle search result
            searchWidget.on("search-complete", function (event) {
                const result = event.results[0];
                if (result) {
                    const latitude = result.results[0].feature.geometry.latitude;
                    const longitude = result.results[0].feature.geometry.longitude;
                    addMarker(latitude, longitude);
                    selectedLat = latitude;
                    selectedLng = longitude;
                }
            });

            // Add street view layer by default
            map.add(streetLayer);

            // Handle view switching
            document.getElementById("viewSwitch").addEventListener("change", function () {
                const selectedView = this.value;
                map.removeAll();

                if (selectedView === "street") {
                    map.add(streetLayer);
                } else if (selectedView === "satellite") {
                    map.add(satelliteLayer);
                } else if (selectedView === "aerial") {
                    map.add(aerialLayer);
                }
            });
        });
    }

    // Initialize ESRI map
    function initializeEsriMap(inputId, mapId, preselectedLat = null, preselectedLng = null) {
        const input = document.getElementById(inputId);
        const mapElement = document.getElementById(mapId);

        fetchToken(clientId, clientSecret).then((token) => {
            initializeMapWithToken(mapElement, token, preselectedLat, preselectedLng, input);
        }).catch(error => {
            console.error("Error obtaining OAuth token:", error);
        });
    }

    // Initialize ESRI map for edit with existing location
    function initializeEsriMapForEdit(inputSelector, mapSelector, address) {
        fetchToken(clientId, clientSecret).then((token) => {
            const geocodeUrl = `https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?f=json&singleLine=${encodeURIComponent(address)}&outFields=Match_addr&token=${token}`;
            
            fetch(geocodeUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.candidates && data.candidates.length > 0) {
                        const location = data.candidates[0].location;
                        initializeMapWithToken($(mapSelector)[0], token, location.y, location.x, $(inputSelector)[0]);
                    } else {
                        initializeMapWithToken($(mapSelector)[0], token, null, null, $(inputSelector)[0]);
                    }
                })
                .catch(error => {
                    console.error("Geocoding error:", error);
                    initializeMapWithToken($(mapSelector)[0], token, null, null, $(inputSelector)[0]);
                });
        }).catch(error => {
            console.error("Token error:", error);
        });
    }

    // =============================================
    // CREATE BUILDING FUNCTIONALITY
    // =============================================

    let selectedFloors = [];
    let floorCount = 0;
    let selectedBasements = [];
    let basementCount = 0;

    // Function to show floor form in create mode
    function showFloorForm() {
        if (floorCount >= 10) {
            alert('You have already added the maximum number of floors.');
            return;
        }

        const floorFieldsContainer = document.getElementById("floorFields");
        const floorDetailDiv = document.createElement('div');
        floorDetailDiv.classList.add('mb-3');
        floorDetailDiv.id = `floor_detail_${floorCount}`;

        floorDetailDiv.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label for="floor_number_${floorCount}">Floor Number</label>
                    <input type="number" name="floor_number[${floorCount}]" id="floor_number_${floorCount}" 
                        class="form-control floor-input" min="1" placeholder="Enter floor number" required>
                </div>
                <div class="col-md-4">
                    <label for="floor_name_${floorCount}">Floor Name</label>
                    <input type="text" name="floor_name[${floorCount}]" id="floor_name_${floorCount}" 
                        class="form-control" placeholder="Enter floor name (optional)">
                </div>
                <div class="col-md-4">
                    <label for="floor_image_${floorCount}">Floor Image</label>
                    <input type="file" name="floor_image[${floorCount}]" id="floor_image_${floorCount}" 
                        class="form-control" accept="image/*">
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-floor">Remove Floor</button>
        `;

        floorFieldsContainer.appendChild(floorDetailDiv);

        // Handle floor number input
        const floorInput = floorDetailDiv.querySelector(`#floor_number_${floorCount}`);
        floorInput.addEventListener('input', function() {
            const selectedFloorNumber = parseInt(this.value);
            
            if (selectedFloorNumber < 1) {
                this.setCustomValidity('Please enter a valid floor number (1 and above).');
                return;
            } else {
                this.setCustomValidity('');
            }

            if (selectedFloors.includes(selectedFloorNumber)) {
                alert(`Floor number ${selectedFloorNumber} has already been entered.`);
                this.value = '';
            } else {
                selectedFloors.push(selectedFloorNumber);
            }
        });

        // Handle floor removal
        floorDetailDiv.querySelector('.remove-floor').addEventListener('click', function() {
            const floorNumber = parseInt(floorInput.value);
            if (floorNumber) {
                selectedFloors = selectedFloors.filter(f => f !== floorNumber);
            }
            floorFieldsContainer.removeChild(floorDetailDiv);
            floorCount--;
            showAddFloorButton();
        });

        floorCount++;
        showAddFloorButton();
    }

    // Show "Add Floor" button in create mode
    function showAddFloorButton() {
        const container = document.getElementById("addFloorButtonContainer");
        container.innerHTML = '';

        const addButton = document.createElement('button');
        addButton.classList.add('btn', 'btn-primary', 'mt-3');
        addButton.textContent = 'Add Floor Detail';
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            showFloorForm();
        });

        container.appendChild(addButton);
    }

    // Function to show basement form in create mode
    function showBasementForm() {
        if (basementCount >= 10) {
            alert('You have already added the maximum number of basements.');
            return;
        }

        const basementFieldsContainer = document.getElementById("basementFields");
        const basementDetailDiv = document.createElement('div');
        basementDetailDiv.classList.add('mb-3');
        basementDetailDiv.id = `basement_detail_${basementCount}`;

        basementDetailDiv.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label for="basement_number_${basementCount}">Basement Number</label>
                    <input type="number" name="basement_number[${basementCount}]" id="basement_number_${basementCount}" 
                        class="form-control basement-input" min="1" placeholder="Enter basement number" required>
                </div>
                <div class="col-md-4">
                    <label for="basement_name_${basementCount}">Basement Name</label>
                    <input type="text" name="basement_name[${basementCount}]" id="basement_name_${basementCount}" 
                        class="form-control" placeholder="Enter basement name (optional)">
                </div>
                <div class="col-md-4">
                    <label for="basement_image_${basementCount}">Basement Image</label>
                    <input type="file" name="basement_image[${basementCount}]" id="basement_image_${basementCount}" 
                        class="form-control" accept="image/*">
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-basement">Remove Basement</button>
        `;

        basementFieldsContainer.appendChild(basementDetailDiv);

        // Handle basement number input
        const basementInput = basementDetailDiv.querySelector(`#basement_number_${basementCount}`);
        basementInput.addEventListener('input', function() {
            const selectedBasementNumber = parseInt(this.value);
            
            if (selectedBasementNumber < 1) {
                this.setCustomValidity('Please enter a valid basement number (1 and above).');
                return;
            } else {
                this.setCustomValidity('');
            }

            if (selectedBasements.includes(selectedBasementNumber)) {
                alert(`Basement number ${selectedBasementNumber} has already been entered.`);
                this.value = '';
            } else {
                selectedBasements.push(selectedBasementNumber);
            }
        });

        // Handle basement removal
        basementDetailDiv.querySelector('.remove-basement').addEventListener('click', function() {
            const basementNumber = parseInt(basementInput.value);
            if (basementNumber) {
                selectedBasements = selectedBasements.filter(b => b !== basementNumber);
            }
            basementFieldsContainer.removeChild(basementDetailDiv);
            basementCount--;
            showAddBasementButton();
        });

        basementCount++;
        showAddBasementButton();
    }

    // Show "Add Basement" button in create mode
    function showAddBasementButton() {
        const container = document.getElementById("addBasementButtonContainer");
        container.innerHTML = '';

        const addButton = document.createElement('button');
        addButton.classList.add('btn', 'btn-primary', 'mt-3');
        addButton.textContent = 'Add Basement Detail';
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            showBasementForm();
        });

        container.appendChild(addButton);
    }

    // Handle create form submission
   $(document).on('submit', '#createBuildingForm', function(e) {
    e.preventDefault();

    const form = $(this);
    const formData = new FormData(this);

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Created!',
                    text: response.message
                }).then(() => {
                    $('#createBuildingModal').modal('hide');
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed!',
                    text: response.message || 'Failed to create business.'
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: xhr.responseJSON?.message || 'An error occurred while creating.'
            });
        }
    });
});

    // =============================================
    // EDIT BUILDING FUNCTIONALITY
    // =============================================

    // Edit Building Modal Handler
    $(document).on('click', '.editBuildingBtn', function() {
    // Get all building data
    const buildingId = $(this).data('id');
    const buildingData = {
        name: $(this).data('building_name'),
        address: $(this).data('building_address'),
        suite: $(this).data('suite_number'),
        zipcode: $(this).data('zipcode'),
        total_floor: $(this).data('total_floor'),
        total_basement: $(this).data('total_basement'),
        floors: $(this).data('floors') || [],
        basements: $(this).data('basement') || []
    };

    // Set form action
    $('#editBuildingForm').attr('action', `/admin/buildings/${buildingId}`);

    
    // Populate basic fields
    $('#edit_building_name').val(buildingData.name);
    $('#edit_building_address').val(buildingData.address);
    $('#edit_suite_number').val(buildingData.suite);
    $('#edit_zipcode').val(buildingData.zipcode);
    
    // Initialize map with existing location
    if (buildingData.address) {
        initializeEsriMapForEdit('edit_building_address', 'editMap', buildingData.address);
    }
    
    // Clear previous fields
    $('#editFloorFields').empty();
    $('#editBasementFields').empty();
    
    // Populate floor fields
    buildingData.floors.forEach((floor, index) => {
        const floorField = `
            <div class="floor-detail mb-3" data-floor-id="${floor.id}">
                <input type="hidden" name="floor_id[${index}]" value="${floor.id}">
                <div class="row">
                    <div class="col-md-4">
                        <label>Floor Name</label>
                        <input type="text" name="floor_name[${index}]" 
                               class="form-control" value="${floor.floor_name || ''}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Floor Image</label>
                        <input type="file" name="floor_image[${index}]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        ${floor.floor_image ? `
                        <div class="d-flex align-items-center mt-4">
                            <img src="/storage/${floor.floor_image}" style="width:50px;height:auto;margin-right:10px;">
                            <button type="button" class="btn btn-sm btn-danger delete-floor-image" data-id="${floor.id}">
                                Delete
                            </button>
                            <input type="hidden" name="existing_floor_image[${index}]" value="${floor.floor_image}">
                        </div>
                        ` : ''}
                        <button type="button" class="btn btn-danger remove-floor mt-2">Remove Floor</button>
                    </div>
                </div>
            </div>
        `;
        $('#editFloorFields').append(floorField);
    });
    
    // Populate basement fields
    buildingData.basements.forEach((basement, index) => {
        const basementField = `
            <div class="basement-detail mb-3" data-basement-id="${basement.id}">
                <input type="hidden" name="basement_id[${index}]" value="${basement.id}">
                <div class="row">
                    <div class="col-md-4">
                        <label>Basement Name</label>
                        <input type="text" name="basement_name[${index}]" 
                               class="form-control" value="${basement.basement_name || ''}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Basement Image</label>
                        <input type="file" name="basement_image[${index}]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        ${basement.basement_image ? `
                        <div class="d-flex align-items-center mt-4">
                            <img src="/storage/${basement.basement_image}" style="width:50px;height:auto;margin-right:10px;">
                            <button type="button" class="btn btn-sm btn-danger delete-basement-image" data-id="${basement.id}">
                                Delete
                            </button>
                            <input type="hidden" name="existing_basement_image[${index}]" value="${basement.basement_image}">
                        </div>
                        ` : ''}
                        <button type="button" class="btn btn-danger remove-basement mt-2">Remove Basement</button>
                    </div>
                </div>
            </div>
        `;
        $('#editBasementFields').append(basementField);
    });

    // Add buttons for new floors/basements
    $('#editAddFloorButtonContainer').html(`
        <button type="button" class="btn btn-primary" id="addFloorBtn">
            Add Floor
        </button>
        <input type="hidden" name="total_floor" value="${buildingData.floors.length}">
    `);
    
    $('#editAddBasementButtonContainer').html(`
        <button type="button" class="btn btn-primary" id="addBasementBtn">
            Add Basement
        </button>
        <input type="hidden" name="total_basement" value="${buildingData.basements.length}">
    `);

    $('#addFloorBtn').click(function() {
    const index = $('.floor-detail').length;

    // Assuming this is how you'd fetch dynamic image paths (this should be from your data store or database)
    const imagePaths = [
        "floor_images/floor_52_1745512879_0.jpg",
        "floor_images/floor_52_1745923975_0.jpg",
        "floor_images/floor_52_1745924122_0.jpg"
    ];

    // Get the image path for the current floor (just an example here)
    const imagePath = imagePaths[index] || ''; // Use the corresponding image or empty string

    // Create the image elements for each image in the array
    let imageElements = '';
    imagePaths.forEach(imagePath => {
        // Clean up the path as per your requirements
        const cleanPath = imagePath.replace(/^public\//, '')
                                   .replace(/^\/storage\//, '')
                                   .replace(/\\/g, '/');

        imageElements += `
            <div style="display: inline-block; margin-right: 10px;">
                <img src="{{ asset('storage') }}/${cleanPath}" 
                     alt="Floor Image" 
                     style="width: 100px; height: auto; cursor: pointer;" 
                     class="floor-image">
            </div>
        `;
    });

    // Now, append the floor detail, along with the dynamic image previews
    $('#editFloorFields').append(`
        <div class="floor-detail mb-3">
            <input type="hidden" name="floor_id[${index}]" value="new">
            <div class="row">
                <div class="col-md-4">
                    <label>Floor Name</label>
                    <input type="text" name="floor_name[${index}]" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Floor Image</label>
                    <input type="file" name="floor_image[${index}]" class="form-control">
                    <!-- Display image previews dynamically -->
                    ${imagePath ? imageElements : ''}
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-danger remove-floor mt-4">Remove Floor</button>
                </div>
            </div>
        </div>
    `);

    // Update the total floor count
    $('input[name="total_floor"]').val(index + 1);
});


    // Handle adding new basements
    $('#addBasementBtn').click(function() {
        const index = $('.basement-detail').length;
        $('#editBasementFields').append(`
            <div class="basement-detail mb-3">
                <input type="hidden" name="basement_id[${index}]" value="new">
                <div class="row">
                    <div class="col-md-4">
                        <label>Basement Name</label>
                        <input type="text" name="basement_name[${index}]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Basement Image</label>
                        <input type="file" name="basement_image[${index}]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger remove-basement mt-4">Remove Basement</button>
                    </div>
                </div>
            </div>
        `);
        $('input[name="total_basement"]').val(index + 1);
    });

    // Handle removal of floors
    $(document).on('click', '.remove-floor', function() {
        const floorDiv = $(this).closest('.floor-detail');
        const floorId = floorDiv.data('floor-id');
        
        if (floorId) {
            $('#editBuildingForm').append(`<input type="hidden" name="deleted_floors[]" value="${floorId}">`);
        }
        
        floorDiv.remove();
        $('input[name="total_floor"]').val($('.floor-detail').length);
    });

    // Handle removal of basements
    $(document).on('click', '.remove-basement', function() {
        const basementDiv = $(this).closest('.basement-detail');
        const basementId = basementDiv.data('basement-id');
        
        if (basementId) {
            $('#editBuildingForm').append(`<input type="hidden" name="deleted_basements[]" value="${basementId}">`);
        }
        
        basementDiv.remove();
        $('input[name="total_basement"]').val($('.basement-detail').length);
    });

    // Handle floor image deletion
    $(document).on('click', '.delete-floor-image', function(e) {
        e.preventDefault();
        const floorId = $(this).data('id');
        $('#editBuildingForm').append(`<input type="hidden" name="deleted_floor_images[]" value="${floorId}">`);
        $(this).closest('.d-flex').remove();
    });

    // Handle basement image deletion
    $(document).on('click', '.delete-basement-image', function(e) {
        e.preventDefault();
        const basementId = $(this).data('id');
        $('#editBuildingForm').append(`<input type="hidden" name="deleted_basement_images[]" value="${basementId}">`);
        $(this).closest('.d-flex').remove();
    });

    // Handle form submission
    $('#editBuildingForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        // Update total counts before submission
        $('input[name="total_floor"]').val($('.floor-detail').length);
        $('input[name="total_basement"]').val($('.basement-detail').length);
        
        // Submit form via AJAX
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Building updated successfully',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Failed to update building',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Failed to update building',
                    icon: 'error'
                });
            }
        });
    });
});

// Initialize ESRI map for edit with existing location
function initializeEsriMapForEdit(inputSelector, mapSelector, address) {
    fetchToken(clientId, clientSecret).then((token) => {
        const geocodeUrl = `https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?f=json&singleLine=${encodeURIComponent(address)}&outFields=Match_addr&token=${token}`;
        
        fetch(geocodeUrl)
            .then(response => response.json())
            .then(data => {
                if (data.candidates && data.candidates.length > 0) {
                    const location = data.candidates[0].location;
                    initializeMapWithToken($(mapSelector)[0], token, location.y, location.x, $(inputSelector)[0]);
                } else {
                    initializeMapWithToken($(mapSelector)[0], token, null, null, $(inputSelector)[0]);
                }
            })
            .catch(error => {
                console.error("Geocoding error:", error);
                initializeMapWithToken($(mapSelector)[0], token, null, null, $(inputSelector)[0]);
            });
    }).catch(error => {
        console.error("Token error:", error);
    });
}

    // Handle edit form submission
    $('#editBuildingForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const buildingId = $(this).attr('action').split('/').pop();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Building updated successfully',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Failed to update building',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Failed to update building',
                    icon: 'error'
                });
            }
        });
    });

    // =============================================
    // INITIALIZATION
    // =============================================

    $(document).ready(function() {
        // Initialize create map
        initializeEsriMap('building_address', 'map');
        
        // Show initial buttons for create form
        showAddFloorButton();
        showAddBasementButton();
        
        // Handle delete confirmation
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const deleteForm = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteForm.submit();
                }
            });
        });

        // Toggle building status
        $('.status-toggle').on('change', function() {
            const buildingId = $(this).data('id');
            const newStatus = $(this).prop('checked') ? 1 : 0;
            const statusText = newStatus === 1 ? 'Approved' : 'Pending';

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to change this building's status to ${statusText}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/buildings/status/${buildingId}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#status-text-' + buildingId).text(statusText);
                                if (newStatus === 1) {
                                    $('#status-text-' + buildingId).removeClass('status-pending').addClass('status-approved');
                                } else {
                                    $('#status-text-' + buildingId).removeClass('status-approved').addClass('status-pending');
                                }
                                Swal.fire('Success!', 'The building status has been updated.', 'success');
                            } else {
                                Swal.fire('Error', 'Failed to update status!', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                        }
                    });
                } else {
                    $(this).prop('checked', !$(this).prop('checked'));
                }
            });
        });
    });
</script>
<script>
    // Function to handle the "View All" button click for Floors
    $('.viewAllFloorsBtn').on('click', function () {
        const floorsData = $(this).data('floors') || [];
        const modalBody = $('#viewAllFloorsModalBody');
        
        modalBody.empty();  // Clear the modal body before adding new content

        if (floorsData.length > 0) {
            floorsData.forEach((floor) => {
                // Parse JSON array or handle single image path
                let images = [];
                try {
                    images = JSON.parse(floor.floor_image) || [];
                } catch (e) {
                    if (floor.floor_image) images = [floor.floor_image];
                }

                // Create image elements for each image
                let imageElements = '';
                images.forEach(imagePath => {
                    const cleanPath = imagePath.replace(/^public\//, '')
                                              .replace(/^\/storage\//, '')
                                              .replace(/\\/g, '/');
                    
                    imageElements += `
                        <div style="display: inline-block; margin-right: 10px;">
                            <img src="{{ asset('storage') }}/${cleanPath}" 
                                 alt="Floor Image" 
                                 style="width: 100px; height: auto; cursor: pointer;" 
                                 class="floor-image">
                        </div>
                    `;
                });

                const floorRow = $(`
                    <tr>
                        <td>${floor.floor_name || 'No name available'}</td>
                        <td>
                            ${imageElements || '<p>No images available</p>'}
                        </td>
                    </tr>
                `);
                modalBody.append(floorRow);
            });
        } else {
            modalBody.append('<tr><td colspan="2">No floors available.</td></tr>');
        }
    });

    // Function to handle the "View All" button click for Basement
    $('.viewAllBasementBtn').on('click', function () {
        const basementData = $(this).data('basement') || [];
        const modalBody = $('#viewAllBasementModalBody');
        
        modalBody.empty();  // Clear the modal body before adding new content

        if (basementData.length > 0) {
            basementData.forEach((basement) => {
                // Parse JSON array or handle single image path
                let images = [];
                try {
                    images = JSON.parse(basement.basement_image) || [];
                } catch (e) {
                    if (basement.basement_image) images = [basement.basement_image];
                }

                // Create image elements for each image
                let imageElements = '';
                images.forEach(imagePath => {
                    const cleanPath = imagePath.replace(/^public\//, '')
                                              .replace(/^\/storage\//, '')
                                              .replace(/\\/g, '/');
                    
                    imageElements += `
                        <div style="display: inline-block; margin-right: 10px;">
                            <img src="{{ asset('storage') }}/${cleanPath}" 
                                 alt="Basement Image" 
                                 style="width: 100px; height: auto; cursor: pointer;" 
                                 class="basement-image">
                        </div>
                    `;
                });

                const basementRow = $(`
                    <tr>
                        <td>${basement.basement_name || 'No name available'}</td>
                        <td>
                            ${imageElements || '<p>No images available</p>'}
                        </td>
                    </tr>
                `);
                modalBody.append(basementRow);
            });
        } else {
            modalBody.append('<tr><td colspan="2">No Basement available.</td></tr>');
        }
    });

    // Image preview functionality for floor images
    $(document).on('click', '.floor-image', function() {
        const imageUrl = $(this).attr('src');
        $('#imagePreview').attr('src', imageUrl);
        $('#imagePreviewModal').modal('show');
    });

    // Image preview functionality for basement images
    $(document).on('click', '.basement-image', function() {
        const imageUrl = $(this).attr('src');
        $('#imagePreview').attr('src', imageUrl);
        $('#imagePreviewModal').modal('show');
    });
</script>

<script>
    // Function to handle the "Gallery" button click
    $(document).on('click', '.galleryBtn', function (event) {
        event.preventDefault();
        
        const floorsData = $(this).data('floors') || [];
        const basementsData = $(this).data('basement') || [];
        const modalBody = $('#galleryModalBody');
        
        modalBody.empty();  // Clear previous content

        let carouselIndicators = '';
        let carouselItems = '';
        let slideIndex = 0;

        // Process floor images
        if (floorsData.length > 0) {
            modalBody.append('<h4>Floor Images</h4>');

            floorsData.forEach((floor, floorIndex) => {
                // Parse JSON array or handle single image path
                let images = [];
                try {
                    images = JSON.parse(floor.floor_image) || [];
                } catch (e) {
                    if (floor.floor_image) images = [floor.floor_image];
                }

                images.forEach((imagePath, imgIndex) => {
                    const cleanPath = imagePath.replace(/^public\//, '')
                                             .replace(/^\/storage\//, '')
                                             .replace(/\\/g, '/');
                    
                    carouselIndicators += `
                        <li data-bs-target="#galleryCarousel" data-bs-slide-to="${slideIndex}" 
                            ${slideIndex === 0 ? 'class="active"' : ''}></li>
                    `;
                    
                    carouselItems += `
                        <div class="carousel-item ${slideIndex === 0 ? 'active' : ''}">
                            <img src="{{ asset('storage') }}/${cleanPath}" 
                                 alt="Floor ${floorIndex+1} Image ${imgIndex+1}" 
                                 class="d-block w-100" 
                                 style="height: 400px; object-fit: contain;">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>${floor.floor_name || 'Floor'} - Image ${imgIndex+1}</h5>
                            </div>
                        </div>
                    `;
                    
                    slideIndex++;
                });
            });
        }

        // Process basement images
        if (basementsData.length > 0) {
            if (floorsData.length > 0) {
                modalBody.append('<hr><h4>Basement Images</h4>');
            } else {
                modalBody.append('<h4>Basement Images</h4>');
            }

            basementsData.forEach((basement, basementIndex) => {
                // Parse JSON array or handle single image path
                let images = [];
                try {
                    images = JSON.parse(basement.basement_image) || [];
                } catch (e) {
                    if (basement.basement_image) images = [basement.basement_image];
                }

                images.forEach((imagePath, imgIndex) => {
                    const cleanPath = imagePath.replace(/^public\//, '')
                                             .replace(/^\/storage\//, '')
                                             .replace(/\\/g, '/');
                    
                    carouselIndicators += `
                        <li data-bs-target="#galleryCarousel" data-bs-slide-to="${slideIndex}" 
                            ${slideIndex === 0 && carouselItems === '' ? 'class="active"' : ''}></li>
                    `;
                    
                    carouselItems += `
                        <div class="carousel-item ${slideIndex === 0 && carouselItems === '' ? 'active' : ''}">
                            <img src="{{ asset('storage') }}/${cleanPath}" 
                                 alt="Basement ${basementIndex+1} Image ${imgIndex+1}" 
                                 class="d-block w-100" 
                                 style="height: 400px; object-fit: contain;">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>${basement.basement_name || 'Basement'} - Image ${imgIndex+1}</h5>
                            </div>
                        </div>
                    `;
                    
                    slideIndex++;
                });
            });
        }

        // Show message if no images found
        if (carouselItems === '') {
            modalBody.append('<p>No images available for this building.</p>');
        } else {
            // Build the carousel
            modalBody.append(`
                <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        ${carouselIndicators}
                    </ol>
                    <div class="carousel-inner">
                        ${carouselItems}
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            `);
        }
    });
</script>

<script>
    // Function to handle the "View All" button click
    $('.viewAllFloorsBtn').on('click', function () {
        const floorsData = $(this).data('floors');  // Existing floor data for the building
        const modalBody = $('#viewAllFloorsModalBody');
        
        modalBody.empty();  // Clear the modal body before adding new content

        if (floorsData.length > 0) {
            floorsData.forEach((floor) => {
                // Parse the JSON array of images if it exists
                let images = [];
                try {
                    images = JSON.parse(floor.floor_image) || [];
                } catch (e) {
                    // If not JSON, treat as single image path
                    if (floor.floor_image) images = [floor.floor_image];
                }

                // Create image elements for each image in the array
                let imageElements = '';
                images.forEach(imagePath => {
                    // Remove any unwanted prefixes and clean the path
                    const cleanPath = imagePath.replace(/^public\//, '')
                                              .replace(/^\/storage\//, '')
                                              .replace(/\\/g, '/');
                    
                    imageElements += `
                        <div style="display: inline-block; margin-right: 10px;">
                            <img src="{{ asset('storage') }}/${cleanPath}" 
                                 alt="Floor Image" 
                                 style="width: 100px; height: auto; cursor: pointer;" 
                                 class="floor-image">
                        </div>
                    `;
                });

                const floorRow = $(`
                    <tr>
                        <td>${floor.floor_name}</td>
                        <td>
                            ${imageElements || '<p>No images available</p>'}
                        </td>
                    </tr>
                `);
                modalBody.append(floorRow);
            });
        } else {
            modalBody.append('<tr><td colspan="2">No floors available.</td></tr>');
        }
    });

    // Function to handle the image click event and show it in the full-screen modal
    $(document).on('click', '.floor-image', function () {
        const imageSrc = $(this).attr('src');
        $('#fullScreenImage').attr('src', imageSrc);
        $('.modal-backdrop').last().remove();
        $('#fullScreenImageModal').modal('show');
    });
</scrip>
 
<script>

$(document).ready(function() {
    var table = $('#buildingTable').DataTable({
        lengthChange: true,  // Allow length change
        pageLength: 10,      // Default number of rows per page
        lengthMenu: [
            [10, 20, 50, -1],  // Values for rows per page (10, 20, 50, and -1 for "All")
            ['10', '20', '50', 'All']  // Custom labels for the options
        ],  // Options for rows per page: 10, 20, 50, and "All"
        buttons: ['copy', 'excel', 'pdf', 'print'],  // Export buttons
        //dom: 'Bfrtip'  // Display buttons (B) at the top of the table
    });

    // Append buttons to the table
    //table.buttons().container().appendTo('#buildingTable_wrapper .col-md-6:eq(0)');
});


</script>
@endsection
