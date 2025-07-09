<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Basement;
use App\Models\DefaultSetting;
use App\Models\User;
use App\Models\StandardIcon;
use App\Models\StandardIconCategory;
use Validator;
use DB;
use Illuminate\Support\Facades\Auth;
class SearchBuildingController extends Controller
{
public function search_building(Request $request)
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


        // Validate incoming request
        $validation = Validator::make($request->all(), [
            'search'    => 'required',
          
        ]);
    
        // If validation fails, return error message
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->first(),
                'data'    => [],
            ]);
        }
    // Get the search query from the request
    $searchQuery = $request->input('search', ''); // Default to an empty string if no search is provided

    // Build the query
    $query = Building::query();

    // Check the role and adjust the query accordingly
    if ($role == 'Editor') {
        $query->where('user_id', $userId);
    } else if ($role == 'Viewer') {
        $query->where('status', '1');
    }

    // If there's a search query, filter by building address (using 'like' for partial matches)
    if ($searchQuery) {
        $query->where('building_address', 'like', '%' . $searchQuery . '%');
    }

    // Fetch the building details based on the conditions
    $buildingDetails = $query->with(['floors:id,building_id,floor_name,floor_image']) // eager load floors with only the required fields
        ->get(['id', 'building_name', 'building_address', 'total_floor', 'status']);

    // Check if no building details found
    if ($buildingDetails->isEmpty()) {
        return response()->json([
            'success' => true, // Keep success true for a 200 response
            'status_code' => 200,
            'message' => 'No buildings found matching your search.',
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

    







}
