<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Basement;
use App\Models\DefaultSetting;
use App\Models\User;
use App\Models\State;
use App\Models\City;
use App\Models\StandardIcon;
use App\Models\StandardIconCategory;
use App\Models\EditorAssignArea;
use Validator;
use DB;
use Illuminate\Support\Facades\Auth;
 

class BuildingController extends Controller
{
    public function add_building(Request $request)
    {
        // Get the authenticated user ID
        $userId = Auth::id();
        $buildingapproval = DefaultSetting::first();  // Get the first record
        $buildingstatus = $buildingapproval->building_approval;  // Access the 'building_approval' field
        $layoutstatus = $buildingstatus == 1 ? '1' : '0';

        // Validate incoming request
        $validation = Validator::make($request->all(), [
            'building_address'    => 'required',
            'total_floor'         => 'nullable|integer',
            'zipcode'             => 'nullable|string|max:10',
            'floor_name'          => 'nullable|array', // This must be an array
            'floor_number'        => 'nullable|array',
            'floor_image'         => 'array', // This must be an array
            'floor_image.*'       => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'basement_image'      => 'array', // This must be an array
            'basement_number'     => 'nullable|array',
            'basement_image.*'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // If validation fails, return error message
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->first(),
                'data'    => [],
            ]);
        }

        // Check if the user is authenticated
        if (!$userId) {
            return response()->json([
                'success'    => false,
                'status_code' => 401,
                'message'    => "User not authenticated",
            ]);
        }

        // Create a new Building object
        $building = new Building();
        $building->user_id = $userId;
        $building->building_name = $request->building_name;
        $building->building_address = $request->building_address;
        $building->zipcode = $request->zipcode ?? NULL; // If zipcode is not provided, set it as NULL
        $building->suite_number = $request->suite_number ?? NULL;
        $building->total_floor = $request->total_floor ?? 1;
        $building->total_basement = $request->total_basement ?? 0;
        $building->lat = $request->lat ?? NULL;
        $building->lon = $request->lon ?? NULL;
        $building->status = $layoutstatus;

        // Save the building data to the database
        $building->save();

        // Ensure floor_names, floor_numbers and floor_images are arrays, even if single values are passed
        $floorNames = is_array($request->floor_name) ? $request->floor_name : [$request->floor_name];
        $floorNumbers = is_array($request->floor_number) ? $request->floor_number : [$request->floor_number];
        $floorImages = is_array($request->file('floor_image')) ? $request->file('floor_image') : [$request->file('floor_image')];

        // Handle case where floor_name and floor_number are not matched
        if (count($floorNames) > 1 && count($floorNumbers) > 1 && count($floorNames) !== count($floorNumbers)) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => "The number of floor names must match the number of floor numbers.",
            ]);
        }

        // If one of them is missing, default to empty or a reasonable value for the missing field
        if (count($floorNames) < count($floorNumbers)) {
            $floorNames = array_merge($floorNames, array_fill(0, count($floorNumbers) - count($floorNames), null));
        }

        if (count($floorNumbers) < count($floorNames)) {
            $floorNumbers = array_merge($floorNumbers, array_fill(0, count($floorNames) - count($floorNumbers), null));
        }

        // Loop through floor names and numbers to create floor records
        if (!empty($request->floor_name) && is_array($request->floor_name) && $request->has('floor_name')) {
            foreach ($floorNames as $index => $floorName) {
                $floor = new Floor();
                $floor->building_id = $building->id;
                $floor->floor_name = $floorName;
                $floor->floor_number = $floorNumbers[$index]; // Store the floor number
                $floor->status = $layoutstatus;

                // Handle file upload for floor_image
                if (isset($floorImages[$index])) {
                    $file = $floorImages[$index];
                    if ($file->isValid()) {
                        $filename = 'floor_' . $building->id . '_' . $index . '.' . $file->getClientOriginalExtension();
                        $path = $file->store('public/floor_images'); // Store file in the 'floor_images' directory
                        $floor->floor_image = $file->store('floor_images');
                    }
                }

                $floor->save();
            }
        }

        // Handle basement data
        $basementNames = is_array($request->basement_name) ? $request->basement_name : [$request->basement_name];
        $basementNumbers = is_array($request->basement_number) ? $request->basement_number : [$request->basement_number];
        $basementImages = is_array($request->file('basement_image')) ? $request->file('basement_image') : [$request->file('basement_image')];

        // Ensure that the number of basement names and basement numbers match
        if (count($basementNames) > 1 && count($basementNumbers) > 1 && count($basementNames) !== count($basementNumbers)) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => "The number of basement names must match the number of basement numbers.",
            ]);
        }

        // If one of them is missing, default to empty or a reasonable value for the missing field
        if (count($basementNames) < count($basementNumbers)) {
            $basementNames = array_merge($basementNames, array_fill(0, count($basementNumbers) - count($basementNames), null));
        }

        if (count($basementNumbers) < count($basementNames)) {
            $basementNumbers = array_merge($basementNumbers, array_fill(0, count($basementNames) - count($basementNumbers), null));
        }

        // Loop through basement names and numbers to create basement records
        if ($request->has('basement_name') && !empty($request->basement_name) && is_array($request->basement_name)) {
            foreach ($basementNames as $index => $basementName) {
                $basement = new Basement();
                $basement->building_id = $building->id;
                $basement->basement_name = $basementName;
                $basement->basement_number = $basementNumbers[$index]; // Store the basement number
                $basement->status = $layoutstatus;

                // Handle file upload for basement_image
                if (isset($basementImages[$index])) {
                    $file = $basementImages[$index];
                    if ($file->isValid()) {
                        $filename = 'basement_' . $building->id . '_' . $index . '.' . $file->getClientOriginalExtension();
                        $path = $file->store('public/basement_images'); // Store file in the 'basement_images' directory
                        $basement->basement_image = $file->store('basement_images');
                    }
                }

                $basement->save();
            }
        }

        // Return a success response with the building data and its floors
        return response()->json([
            'success'     => true,
            'status_code' => 200,
            'message'     => "Building uploaded successfully",
            'data'        => [
                'building' => $building->only(['id', 'user_id', 'building_name', 'building_address', 'total_floor']),
                'floors'   => $building->floors()->get(),  // Assuming you have a relationship with Floor
                'basements' => $building->basements()->get(),
            ],
        ], 200);
    }



public function deleteFloorImage(Request $request)
{
    $request->validate([
        'floor_id' => 'required|integer|exists:floors,id',
    ]);

    $floor = Floor::find($request->floor_id);

    if (!$floor) {
        return response()->json([
            'success' => false,
            'message' => 'Floor not found.',
        ], 404);
    }

    if ($floor->floor_image && \Storage::exists('public/' . $floor->floor_image)) {
        \Storage::delete('public/' . $floor->floor_image);
    }

    $floor->floor_image = null;
    $floor->save();

    return response()->json([
        'success' => true,
        'message' => 'Floor image deleted successfully.',
    ]);
}

public function deleteBasementImage(Request $request)
{
    $request->validate([
        'basement_id' => 'required|integer|exists:basements,id',
    ]);

    $basement = Basement::find($request->basement_id);

    if (!$basement) {
        return response()->json([
            'success' => false,
            'message' => 'Basement not found.',
        ], 404);
    }

    if ($basement->basement_image && \Storage::exists('public/' . $basement->basement_image)) {
        \Storage::delete('public/' . $basement->basement_image);
    }

    $basement->basement_image = null;
    $basement->save();

    return response()->json([
        'success' => true,
        'message' => 'Basement image deleted successfully.',
    ]);
}


 




    public function getBuildingDetail()
    {
        // Get the authenticated user ID
        $user = Auth::user();
        $role = $user->role;
        $userId = Auth::id();
        // Check if the user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'User not authenticated',
            ], 401);
        }

        $editorArea = EditorAssignArea::where('user_id', $userId)->get();
        $stateIds = $editorArea->pluck('state')->toArray();
        $cityIds = $editorArea->pluck('city')->toArray();

        $states = State::whereIn('id', $stateIds)->pluck('name')->toArray();
        $cities = City::whereIn('id', $cityIds)->pluck('name')->toArray();


        // Fetch the building details associated with the authenticated user
        if ($role == 'Editor') {
            $buildingDetails = Building::where('user_id', $userId)
                ->orWhere(function ($query) use ($states, $cities) {
                    foreach ($states as $state) {
                        $query->orWhere('building_address', 'LIKE', "%{$state}%");
                    }
                    foreach ($cities as $city) {
                        $query->orWhere('building_address', 'LIKE', "%{$city}%");
                    }
                })
                ->with(['floors:id,building_id,floor_name,floor_number,floor_image'])
                ->get(['id', 'building_name', 'building_address', 'zipcode', 'total_floor', 'lat', 'lon', 'status']);
        } else if ($role == 'Viewer') {
            $buildingDetails = Building::where('status', '1')
                ->with(['floors:id,building_id,floor_name,floor_number,floor_image']) // eager load floors with only the required fields
                ->get(['id', 'building_name', 'building_address', 'zipcode', 'total_floor', 'lat', 'lon']);
        }

        // Check if no building details found
        if ($buildingDetails->isEmpty()) {
            return response()->json([
                'success' => true, // Keep success true for a 200 response
                'status_code' => 200,
                'message' => 'No buildings found for this user.',
                'data' => [] // Ensure an empty array is returned for the 'data' key
            ], 200);
        }

        // Iterate through each building and process the floors
        foreach ($buildingDetails as $building) {
            // Get the first floor image if available
            $firstFloorImage = null;
            if ($building->floors->isNotEmpty()) {
                $firstFloor = $building->floors->first();
                if ($firstFloor && $firstFloor->floor_image) {
                    $firstFloorImage = asset('storage/floor_images/' . basename($firstFloor->floor_image));
                }
            }

            $firstBasementImage = null;
            if ($building->basements->isNotEmpty()) {
                $firstBasement = $building->basements->first();
                if ($firstBasement && $firstBasement->basement_image) {
                    $firstBasementImage = asset('storage/basement_images/' . basename($firstBasement->basement_image));
                }
            }

            // Add the first floor image before the 'floors' array
            $building->icon_image = $firstFloorImage;

            // Iterate through the floors and replace 'public' with 'storage' in floor images
            foreach ($building->floors as $floor) {
                if ($floor->floor_image) {
                    $floor->floor_image = asset('storage/floor_images/' . basename($firstFloor->floor_image));
                }
            }

            foreach ($building->basements as $basement) {
                if ($basement->basement_image) {
                    $basement->basement_image = asset('storage/basement_images/' . basename($firstBasement->basement_image));
                }
            }
        }

        // Hide the created_at and updated_at fields from the response
        $buildingDetails->makeHidden(['created_at', 'updated_at']);

        // Return the building details in the response
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'Buildings List',
            'data' => $buildingDetails,
        ], 200);
    }




    public function getStandardIcons()
    {
        $user = Auth::user();
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'User  not authenticated',
            ], 401);
        }

        // Eager load the icons and their categories
        $categories = StandardIconCategory::with('icons.category')->get();

        // Filter out categories that have no icons
        $categoriesWithIcons = $categories->filter(function ($category) {
            return $category->icons->isNotEmpty();  // Only keep categories that have at least one icon
        });

        // Map the categories and icons
        $responseData = $categoriesWithIcons->map(function ($category) {
            $icons = $category->icons->map(function ($icon) {
                // Get the category name, checking if the category exists
                $categoryName = $icon->category ? $icon->category->category_name : 'No Category';

                $imagePath = str_replace('public/', '', 'storage/' . $icon->path);

                return [
                    'icon_id' => $icon->id,
                    'icon_name' => $icon->name,
                    'icon_image_url' => url($imagePath),
                    'category_name' => $categoryName,
                ];
            });
            // Fetch the category image (assuming it's an attribute like `category_image` in your Category model)
            $categoryImagePath = $category->category_image ? url('storage/' . str_replace('public/', '', $category->category_image)) : null;

            return [
                'category_name' => $category->category_name,
                'category_image' => $categoryImagePath,  // Category name from category model
                'icons' => $icons,
            ];
        })->values(); // Use values() to reset the keys

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'Standard Icons List',
            'data' => $responseData,
        ], 200);
    }

    public function GetBuildingGallery(Request $request)
    {
        $data = $request->all();

        // Validate incoming request
        $validation = Validator::make($request->all(), [
            'building_id' => 'required',
        ]);

        // If validation fails, return error message
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->first(),
                'data'    => [],
            ]);
        }

        // Get the authenticated user ID
        $userId = Auth::id();

        // Check if the user is authenticated
        if (!$userId) {
            return response()->json([
                'success'    => false,
                'status_code' => 401,
                'message'    => "User not authenticated",
            ]);
        }

        // Retrieve the floors for the building
        $floors = Floor::where('building_id', $data['building_id'])
            ->orderBy('id', 'desc')
            ->get(['floor_name', 'id', 'floor_image']);

        // Retrieve the basements for the building
        $basements = Basement::where('building_id', $data['building_id'])
            ->orderBy('id', 'desc')
            ->get(['basement_name', 'id', 'basement_image']);

        // Normalize floor data to match the key structure for basements
        $floors = $floors->map(function ($floor) {
            return [
                'name'  => $floor->floor_name,
                'id'    => $floor->id,
                'image' => !empty($floor->floor_image) ? asset('storage/floor_images/' . basename($floor->floor_image)) : null,
            ];
        });

        // Normalize basement data to match the key structure for floors
        $basements = $basements->map(function ($basement) {
            return [
                'name'  => $basement->basement_name,
                'id'    => $basement->id,
                'image' => !empty($basement->basement_image) ? asset('storage/basement_images/' . basename($basement->basement_image)) : null,
            ];
        });

        // Combine floors and basements into a single array
        $combinedGallery = $floors->merge($basements);

        // Return a success response with the combined building gallery data
        return response()->json([
            'success'     => true,
            'status_code' => 200,
            'message'     => "Building Gallery Loaded successfully",
            'data'        => $combinedGallery,
        ], 200);
    }
    public function searchBuilding(Request $request)
    {
        $data = $request->all();

        // Validate incoming request, make both fields optional
        $validation = Validator::make($request->all(), [
            'building_name' => 'nullable|string|max:255',
            'building_address' => 'nullable|string|max:255',
        ]);

        // If validation fails, return error message
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->first(),
                'data'    => [],
            ]);
        }

        // Get the authenticated user ID
        $userId = Auth::id();

        // Check if the user is authenticated
        if (!$userId) {
            return response()->json([
                'success'    => false,
                'status_code' => 401,
                'message'    => "User not authenticated",
            ]);
        }

        // Initialize query to retrieve buildings
        $query = Building::query();

        // If business_name is provided, apply the filter
        if (!empty($data['building_name'])) {
            $query->where('building_name', 'LIKE', '%' . $data['building_name'] . '%');
        }

        // If business_address is provided, apply the filter
        if (!empty($data['building_address'])) {
            $query->where('building_address', 'LIKE', '%' . $data['building_address'] . '%');
        }

        // Execute the query and get the results
        $buildings = $query->get(['id', 'building_name', 'building_address', 'lat', 'lon']);

        // If no buildings found, return an appropriate message
        if ($buildings->isEmpty()) {
            return response()->json([
                'success'     => false,
                'status_code' => 404,
                'message'     => "No buildings found matching the search criteria",
                'data'        => [],
            ]);
        }

        // Return success response with building data including lan and log
        return response()->json([
            'success'     => true,
            'status_code' => 200,
            'message'     => "Buildings found successfully",
            'data'        => $buildings,
        ], 200);
    }




    public function update_building(Request $request, $buildingId)
    {
        // Get the authenticated user ID
        $userId = Auth::id();

        // Find the building
        $building = Building::where('id', $buildingId)->where('user_id', $userId)->first();

        if (!$building) {
            return response()->json([
                'success' => false,
                'status_code' => 404,
                'message' => "Building not found or unauthorized access",
            ]);
        }

        // Validate incoming request
        $validation = Validator::make($request->all(), [
            'building_name'   => 'nullable|string',
            'building_address' => 'nullable|string',
            'total_floor'      => 'nullable|integer',
            'zipcode'          => 'nullable|string|max:10',
            'floor_name'       => 'nullable|array',
            'floor_image'      => 'array',
            'floor_image.*'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'basement_name'    => 'nullable|array',
            'basement_image'   => 'array',
            'basement_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->first(),
                'data'    => [],
            ]);
        }

        // Update building details
        $building->building_name = $request->building_name ?? $building->building_name;
        $building->building_address = $request->building_address ?? $building->building_address;
        $building->zipcode = $request->zipcode ?? $building->zipcode;
        $building->total_floor = $request->total_floor ?? $building->total_floor;
        $building->lat = $request->lat ?? $building->lat;
        $building->lon = $request->lon ?? $building->lon;
        $building->save();

        // Update floors if provided
        if ($request->has('floor_name') && is_array($request->floor_name)) {
            foreach ($request->floor_name as $index => $floorName) {
                $floor = Floor::where('building_id', $building->id)->skip($index)->first();
                if ($floor) {
                    $floor->floor_name = $floorName;
                    $floor->number     = $request->floor_number;
                    if ($request->hasFile("floor_image.$index")) {
                        $file = $request->file("floor_image.$index");
                        if ($file->isValid()) {
                            $floor->floor_image = $file->store('floor_images');
                        }
                    }
                    $floor->save();
                }
            }
        }

        // Update basements if provided
        if ($request->has('basement_name') && is_array($request->basement_name)) {
            foreach ($request->basement_name as $index => $basementName) {
                $basement = Basement::where('building_id', $building->id)->skip($index)->first();
                if ($basement) {
                    $basement->basement_name = $basementName;
                    $basement->basement_number = $request->basement_number;
                    if ($request->hasFile("basement_image.$index")) {
                        $file = $request->file("basement_image.$index");
                        if ($file->isValid()) {
                            $basement->basement_image = $file->store('basement_images');
                        }
                    }
                    $basement->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => "Building updated successfully",
            'data' => [
                'building' => $building->only(['id', 'user_id', 'building_name', 'building_address', 'total_floor','lat','lon']),
                'floors' => $building->floors()->get(),
                'basements' => $building->basements()->get(),
            ],
        ], 200);
    }



    // ================Api for Edit Building===============================
    public function edit_building(Request $request)
    {
        // Validate incoming request
        $validation = Validator::make($request->all(), [
            'building_id'      => 'required|exists:buildings,id',
            'building_name'    => 'nullable|string|max:255',
            'building_address' => 'nullable|string|max:255',
            'total_floor'      => 'nullable|integer',
            'zipcode'          => 'nullable|string|max:10',
            'floor_name'       => 'nullable|array',
            'floor_image'      => 'nullable|array',
            'floor_image.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'floor_id'         => 'nullable|array',  // New validation rule for floor_id
            'basement_name'    => 'nullable|array',
            'basement_image'   => 'nullable|array',
            'basement_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'basement_id'      => 'nullable|array',  // New validation rule for basement_id
        ]);

        // If validation fails, return error message
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->first(),
                'data'    => [],
            ]);
        }

        // Find the building by ID
        $building = Building::find($request->building_id);
        if (!$building) {
            return response()->json([
                'success' => false,
                'status_code' => 404,
                'message' => "Building not found",
            ]);
        }

        // Update building fields if provided
        if ($request->has('building_name')) {
            $building->building_name = $request->building_name;
        }

        if ($request->has('building_address')) {
            $building->building_address = $request->building_address;
        }

        if ($request->has('zipcode')) {
            $building->zipcode = $request->zipcode;
        }

        if ($request->has('total_floor')) {
            $building->total_floor = $request->total_floor;
        }

        if ($request->has('lat')) {
            $building->lat = $request->lat;
        }

if ($request->has('lon')) {
            $building->lon = $request->lon;
        }




        $building->save();

        // Handle the floors
        if ($request->has('floor_name') && is_array($request->floor_name)) {
            $floorNames = $request->floor_name;
            $floorImages = is_array($request->file('floor_image')) ? $request->file('floor_image') : [$request->file('floor_image')];
            $floorIds = $request->has('floor_id') ? $request->floor_id : [];

            foreach ($floorNames as $index => $floorName) {
                // Check if a floor_id is provided
                $floorId = isset($floorIds[$index]) ? $floorIds[$index] : null;

                if ($floorId) {
                    // Update existing floor
                    $floor = Floor::where('id', $floorId)->where('building_id', $building->id)->first();
                    if ($floor) {
                        $floor->floor_name = $floorName;

                        // Handle floor image upload if provided
                        if (isset($floorImages[$index])) {
                            $file = $floorImages[$index];
                            if ($file->isValid()) {
                                $filename = 'floor_' . $building->id . '_' . $floorId . '.' . $file->getClientOriginalExtension();
                                $file->storeAs('public/floor_images', $filename);
                                $floor->floor_image = $filename;
                            }
                        }

                        $floor->save();
                    }
                } else {
                    // No floor_id provided, create a new floor
                    $floor = new Floor();
                    $floor->building_id = $building->id;
                    $floor->floor_name  = $floorName;

                    // Handle floor image upload if provided
                    if (isset($floorImages[$index])) {
                        $file = $floorImages[$index];
                        if ($file->isValid()) {
                            $filename = 'floor_' . $building->id . '_' . $index . '.' . $file->getClientOriginalExtension();
                            $file->storeAs('public/floor_images', $filename);
                            $floor->floor_image = $filename;
                        }
                    }

                    $floor->save();
                }
            }
        }

        // Handle the basements
        if ($request->has('basement_name') && is_array($request->basement_name)) {
            $basementNames = $request->basement_name;
            $basementImages = is_array($request->file('basement_image')) ? $request->file('basement_image') : [$request->file('basement_image')];
            $basementIds = $request->has('basement_id') ? $request->basement_id : [];

            foreach ($basementNames as $index => $basementName) {
                // Check if a basement_id is provided
                $basementId = isset($basementIds[$index]) ? $basementIds[$index] : null;

                if ($basementId) {
                    // Update existing basement
                    $basement = Basement::where('id', $basementId)->where('building_id', $building->id)->first();
                    if ($basement) {
                        $basement->basement_name = $basementName;

                        // Handle basement image upload if provided
                        if (isset($basementImages[$index])) {
                            $file = $basementImages[$index];
                            if ($file->isValid()) {
                                $filename = 'basement_' . $building->id . '_' . $basementId . '.' . $file->getClientOriginalExtension();
                                $file->storeAs('public/basement_images', $filename);
                                $basement->basement_image = $filename;
                            }
                        }

                        $basement->save();
                    }
                } else {
                    // No basement_id provided, create a new basement
                    $basement = new Basement();
                    $basement->building_id = $building->id;
                    $basement->basement_name  = $basementName;

                    // Handle basement image upload if provided
                    if (isset($basementImages[$index])) {
                        $file = $basementImages[$index];
                        if ($file->isValid()) {
                            $filename = 'basement_' . $building->id . '_' . $index . '.' . $file->getClientOriginalExtension();
                            $file->storeAs('public/basement_images', $filename);
                            $basement->basement_image = $filename;
                        }
                    }

                    $basement->save();
                }
            }
        }

        // Return a success response with the updated building data, floors, and basements
        return response()->json([
            'success'     => true,
            'status_code' => 200,
            'message'     => "Building updated successfully",
            'data'        => [
                'building'  => $building->only(['id', 'user_id', 'building_name', 'building_address', 'total_floor','lat','lon']),
                'floors'    => $building->floors()->get(),
                'basements' => $building->basements()->get(),
            ],
        ], 200);
    }

    // ========================Floor Detail Upload==============================
    public function uploadFloorDetail(Request $request)
    {
        DB::beginTransaction();

        if (!Auth::check()) {
            return response()->json([
                'success'     => false,
                'status_code' => 401,
                'message'     => 'User not authenticated',
            ]);
        }

        $validation = Validator::make($request->all(), [
            'building_id' => 'required|integer',

            // Floor validation (optional, but if present must be valid)
            'floor_id'      => 'nullable|integer|exists:floors,id',
            'floor_name'    => 'nullable|string',
            'floor_number'  => 'nullable|string',
            'floor_image'   => 'nullable|array',
            'floor_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'message'       => 'nullable|array',
            'message.*'     => 'nullable|string',

            // Basement validation (optional, but if present must be valid)
            'basement_id'      => 'nullable|integer|exists:basement,id',
            'basement_name'    => 'nullable|string',
            'basement_number'  => 'nullable|string',
            'basement_image'   => 'nullable|array',
            'basement_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'basementmessage'       => 'nullable|array',
            'basementmessage.*'     => 'nullable|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
                'data'    => [],
            ]);
        }

        try {
            $buildingId = $request->building_id;
            $floorData = [];
            $basementData = [];

            // -------------------- Floor Section ---------------------
            if ($request->filled('floor_id')) {
                $floor = Floor::where('id', $request->floor_id)
                    ->where('building_id', $buildingId)
                    ->firstOrFail();

                $imagePaths = is_array(json_decode($floor->floor_image, true)) ? json_decode($floor->floor_image, true) : [];
                $messages = is_array(json_decode($floor->message, true)) ? json_decode($floor->message, true) : [];

                if ($request->hasFile('floor_image')) {
                    foreach ($request->file('floor_image') as $index => $file) {
                        if ($file->isValid()) {
                            $filename = 'floor_' . $buildingId . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('floor_images', $filename, 'public');
                            $imagePaths[] = $path;
                            $messages[] = $request->message[$index] ?? null;
                        }
                    }
                }

                $floor->floor_image = json_encode($imagePaths);
                $floor->message = json_encode($messages);
                $floor->save();

                $floorData = $floor->only(['id', 'building_id', 'floor_name', 'floor_number', 'floor_image', 'message']);
            }

            // -------------------- Basement Section ---------------------
            if ($request->filled('basement_id')) {
                $basement = Basement::where('id', $request->basement_id)
                    ->where('building_id', $buildingId)
                    ->first();

                if (!$basement) {
                    DB::rollBack();
                    return response()->json([
                        'success'     => false,
                        'status_code' => 404,
                        'message'     => 'Basement not found for the given building ID',
                    ]);
                }

                // only continue if basement exists
                $basementPaths = is_array(json_decode($basement->basement_image, true)) ? json_decode($basement->basement_image, true) : [];
                $basementMessages = is_array(json_decode($basement->message, true)) ? json_decode($basement->message, true) : [];

                if ($request->hasFile('basement_image')) {
                    foreach ($request->file('basement_image') as $index => $file) {
                        if ($file->isValid()) {
                            $filename = 'basement_' . $buildingId . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('basement_images', $filename, 'public');
                            $basementPaths[] = $path;
                            // $basementMessages[] = $request->basementmessage[$index] ?? null;
                        }
                    }
                }

                if ($request->filled('basementmessage')) {
                    foreach ($request->basementmessage as $msg) {
                        $basementMessages[] = $msg;
                    }
                }

                $basement->basement_image = json_encode($basementPaths);
                $basement->message = json_encode($basementMessages);
                $basement->save();

                $basementData = $basement->only(['id', 'building_id', 'basement_name', 'basement_number', 'basement_image', 'message']);
            }

            DB::commit();

            return response()->json([
                'success'     => true,
                'status_code' => 200,
                'message'     => 'Data updated successfully',
                'data'        => [
                    'floor'    => $floorData,
                    'basement' => $basementData,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'     => false,
                'status_code' => 500,
                'message'     => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }



    public function getFloorDetailByBuilding(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'User not authenticated',
            ], 401);
        }

        $request->validate([
            'building_id'  => 'required|integer',
            'floor_id'     => 'nullable|integer',
            'basement_id'  => 'nullable|integer',
        ]);

        $buildingId = $request->building_id;

        $floorData = [];
        $basementData = [];

        // ----------------- Floor Section -----------------
        if ($request->filled('floor_id')) {
            $floor = Floor::where('id', $request->floor_id)
                ->where('building_id', $buildingId)
                ->first();

            if ($floor) {
                $floorImages = [];
                try {
                    $imagesRaw = is_string($floor->floor_image)
                        ? json_decode($floor->floor_image, true)
                        : $floor->floor_image;

                    if (is_array($imagesRaw)) {
                        foreach ($imagesRaw as $img) {
                            $floorImages[] = asset('storage/' . ltrim(str_replace('\\', '/', $img), '/'));
                        }
                    } elseif (!empty($floor->floor_image)) {
                        $floorImages[] = asset('storage/' . ltrim(str_replace('\\', '/', $floor->floor_image), '/'));
                    }
                } catch (\Throwable $e) {
                    if (!empty($floor->floor_image)) {
                        $floorImages[] = asset('storage/' . ltrim(str_replace('\\', '/', $floor->floor_image), '/'));
                    }
                }

                $messages = [];
                try {
                    if (!is_null($floor->message)) {
                        $messagesRaw = is_string($floor->message)
                            ? json_decode($floor->message, true)
                            : $floor->message;

                        if (json_last_error() === JSON_ERROR_NONE && is_array($messagesRaw)) {
                            $messages = $messagesRaw;
                        } elseif (!empty($messagesRaw)) {
                            $messages[] = $messagesRaw;
                        }
                    }
                } catch (\Throwable $e) {
                    if (!empty($floor->message)) {
                        $messages[] = $floor->message;
                    }
                }

                $floorData = [
                    'id'           => $floor->id,
                    'building_id'  => $floor->building_id,
                    'floor_name'   => $floor->floor_name,
                    'floor_image'  => $floorImages,
                    'message'      => $messages,
                ];
            }
        }

        // ----------------- Basement Section -----------------
        if ($request->filled('basement_id')) {
            $basement = Basement::where('id', $request->basement_id)
                ->where('building_id', $buildingId)
                ->first();

            if ($basement) {
                $basementImages = [];
                try {
                    $imagesRaw = is_string($basement->basement_image)
                        ? json_decode($basement->basement_image, true)
                        : $basement->basement_image;

                    if (is_array($imagesRaw)) {
                        foreach ($imagesRaw as $img) {
                            $basementImages[] = asset('storage/' . ltrim(str_replace('\\', '/', $img), '/'));
                        }
                    } elseif (!empty($basement->basement_image)) {
                        $basementImages[] = asset('storage/' . ltrim(str_replace('\\', '/', $basement->basement_image), '/'));
                    }
                } catch (\Throwable $e) {
                    if (!empty($basement->basement_image)) {
                        $basementImages[] = asset('storage/' . ltrim(str_replace('\\', '/', $basement->basement_image), '/'));
                    }
                }

                $messages = [];
                try {
                    if (!is_null($basement->message)) {
                        $messagesRaw = is_string($basement->message)
                            ? json_decode($basement->message, true)
                            : $basement->message;

                        if (json_last_error() === JSON_ERROR_NONE && is_array($messagesRaw)) {
                            $messages = $messagesRaw;
                        } elseif (!empty($messagesRaw)) {
                            $messages[] = $messagesRaw;
                        }
                    }
                } catch (\Throwable $e) {
                    if (!empty($basement->message)) {
                        $messages[] = $basement->message;
                    }
                }

                $basementData = [
                    'id'              => $basement->id,
                    'building_id'     => $basement->building_id,
                    'basement_name'   => $basement->basement_name,
                    'basement_image'  => $basementImages,
                    'message'         => $messages,
                ];
            }
        }

        return response()->json([
            'success'     => true,
            'status_code' => 200,
            'message'     => 'Details fetched successfully.',
            'data'        => [
                'floor'    => $floorData,
                'basement' => $basementData,
            ],
        ], 200);
    }





public function deleteFloorBasementImage(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => 'User not authenticated',
        ], 401);
    }

    $request->validate([
        'building_id'  => 'required|integer|exists:buildings,id',
        'floor_id'     => 'nullable|integer|exists:floors,id',
        'basement_id'  => 'nullable|integer|exists:basement,id',
        'image_path'   => 'nullable|string',
        'delete_all'   => 'nullable|boolean',
    ]);

    if (!$request->floor_id && !$request->basement_id) {
        return response()->json([
            'success' => false,
            'status_code' => 422,
            'message' => 'Either floor_id or basement_id must be provided.',
        ]);
    }

    $isFloor = !empty($request->floor_id);
    $model = $isFloor
        ? Floor::where('id', $request->floor_id)->where('building_id', $request->building_id)->first()
        : Basement::where('id', $request->basement_id)->where('building_id', $request->building_id)->first();

    if (!$model) {
        return response()->json([
            'success' => false,
            'status_code' => 404,
            'message' => 'Record not found.',
        ]);
    }

    $imageField = $isFloor ? 'floor_image' : 'basement_image';
    $images = is_string($model->$imageField)
        ? json_decode($model->$imageField, true)
        : $model->$imageField;

    if (!is_array($images)) {
        $images = [];
    }

    $imageToDelete = $request->image_path;
    $deleteAll = $request->boolean('delete_all', false);

    $updatedImages = [];

    foreach ($images as $img) {
        $fullPath = 'public/' . ltrim($img, '/');
        if ($deleteAll || ($imageToDelete && str_contains($img, $imageToDelete))) {
            Storage::delete($fullPath); // Delete file from disk
            continue; // Skip adding to updated images
        }
        $updatedImages[] = $img; // Keep image
    }

    $model->$imageField = json_encode($updatedImages);
    $model->save();

    return response()->json([
        'success' => true,
        'status_code' => 200,
        'message' => $deleteAll
            ? 'All images deleted successfully.'
            : 'Selected image deleted successfully.',
        'remaining_images' => $updatedImages,
    ]);
}








    public function deleteBasement(Request $request)
{
    DB::beginTransaction();

    if (!Auth::check()) {
        return response()->json([
            'success'     => false,
            'status_code' => 401,
            'message'     => 'User not authenticated',
        ]);
    }

    $validation = Validator::make($request->all(), [
        'building_id' => 'required|integer|exists:buildings,id',
        'basement_id' => 'required|integer|exists:basement,id',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validation->errors()->first(),
            'data'    => [],
        ]);
    }

    try {
        $basement = Basement::where('id', $request->basement_id)
                    ->where('building_id', $request->building_id)
                    ->firstOrFail();

        // Delete associated images
        $imagePaths = json_decode($basement->basement_image, true) ?? [];
        foreach ($imagePaths as $path) {
            Storage::disk('public')->delete($path);
        }

        $basement->delete();

        DB::commit();

        return response()->json([
            'success'     => true,
            'status_code' => 200,
            'message'     => 'Basement deleted successfully',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success'     => false,
            'status_code' => 500,
            'message'     => 'Something went wrong: ' . $e->getMessage(),
        ]);
    }
}
}
