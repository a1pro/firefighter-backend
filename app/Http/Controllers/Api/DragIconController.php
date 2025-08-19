<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DragIcon;
use App\Models\Floor;
use App\Models\Basement;
use App\Models\StandardIcon;
use App\Models\StandardIconCategory;
use App\Models\DragIconResize;
use Auth;
use Validator;
use DB;
use Illuminate\Support\Facades\Storage;


class DragIconController extends Controller
{
    
    
public function dragIconSave(Request $request)
    {
 
        $userId = Auth::id();
    
   
        // Validation
        $validation = Validator::make($request->all(), [
            'icon_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
    
        // Return validation error response
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => $validation->errors()->first(),
                'data' => [],
            ]);
        }
    
        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'User Not Authenticated',
                'data' => [],
            ]);
        }
    
        // Create new DragIcon record
        $icondrag = new DragIcon();
        $icondrag->user_id = $userId;
        $icondrag->building_id = $request->building_id;
        $icondrag->floor_id = $request->floor_id;
        $icondrag->basement_id = $request->basement_id;
        $icondrag->icon_id = $request->icon_id;
        $icondrag->latitude = $request->latitude;
        $icondrag->longitude = $request->longitude;
        $icondrag->message = $request->message ?? null;
        $icondrag->rotation = $request->rotation ?? null;

    
        // Save the record
        if ($icondrag->save()) {
            return response()->json([
                'success' => true,
                'status_code' => 200,
                'message' => 'Data Saved Successfully',
                'data' => $icondrag,
            ]);
        }
    
        // If saving fails
        return response()->json([
            'success' => false,
            'status_code' => 500,
            'message' => 'Failed to Save Data',
            'data' => [],
        ]);
    }
    
// ================================Drag Icon Get====================================
public function getDragIcons(Request $request)
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => 'User not authenticated',
        ], 401);
    }

    // Validate request
    $request->validate([
        'building_id' => 'required|integer|exists:buildings,id',
        'floor_id' => 'nullable|integer|exists:floors,id|required_without:basement_id',
        'basement_id' => 'nullable|integer|exists:basement,id|required_without:floor_id',
    ]);

    $buildingId = $request->input('building_id');
    $floorId = $request->input('floor_id');
    $basementId = $request->input('basement_id');

    // Start query
    $query = DragIcon::where('building_id', $buildingId)
        ->where('user_id', $userId)
        ->with(['icon.category']); // Load icon and its category

    // Apply floor/basement filters
    if ($floorId) {
        $query->where('floor_id', $floorId)->whereNull('basement_id');
    } elseif ($basementId) {
        $query->where('basement_id', $basementId)->whereNull('floor_id');
    }

    $dragIcons = $query->get();

    if ($dragIcons->isEmpty()) {
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'No Drag Icons found for the provided filters',
            'data' => [],
        ], 200);
    }

    // Format response data
    $responseData = $dragIcons->map(function ($dragIcon) {
        $icon = $dragIcon->icon;
        $category = $icon?->category;
        $imagePath = $icon ? str_replace('public/', '', 'storage/' . $icon->path) : null;

        return [
             'drag_icon_id'      => $dragIcon->id,
            'icon_id' => $icon?->id,
            'icon_name' => $icon?->name ?? 'Unknown',
            'icon_image_url' => $imagePath ? url($imagePath) : null,
            'category_name' => $category?->category_name ?? 'No Category',
            'latitude' => $dragIcon->latitude,
            'longitude' => $dragIcon->longitude,
            'message'  => $dragIcon->message,
            'floor_id' => $dragIcon->floor_id,
            'basement_id' => $dragIcon->basement_id,
        ];
    })->values();

    return response()->json([
        'success' => true,
        'status_code' => 200,
        'message' => 'Drag Icons List',
        'data' => $responseData,
    ], 200);
}


    
// ========================================================================================
public function getDragReactIcons(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success'     => false,
            'status_code' => 401,
            'message'     => 'User not authenticated',
        ], 401);
    }

    $request->validate([
        'building_id' => 'required|integer|exists:buildings,id',
    ]);

    $buildingId = $request->query('building_id');
    $floorId    = $request->query('floor_id');
    $basementId = $request->query('basement_id');

    $query = DragIcon::where('building_id', $buildingId)
        ->with(['icon.category']);

    // Remove this block so Editors see all data:
    // if ($user->role == 'Editor') {
    //     $query->where('user_id', $user->id);
    // }

    if ($floorId !== null) {
        $floorExists = DragIcon::where('floor_id', $floorId)
            ->where('building_id', $buildingId)
            ->exists();

        if (!$floorExists) {
            return response()->json([
                'success'     => false,
                'status_code' => 404,
                'message'     => 'Floor not found or does not belong to the provided building',
            ], 404);
        }

        $query->where('floor_id', $floorId);
    }

    if ($basementId !== null) {
        $basementExists = DragIcon::where('basement_id', $basementId)
            ->where('building_id', $buildingId)
            ->exists();

        if (!$basementExists) {
            return response()->json([
                'success'     => false,
                'status_code' => 404,
                'message'     => 'Basement not found or does not belong to the provided building',
            ], 404);
        }

        $query->where('basement_id', $basementId);
    }

    $dragIcons = $query->get();

    if ($dragIcons->isEmpty()) {
        return response()->json([
            'success'     => true,
            'status_code' => 200,
            'message'     => 'No Drag Icons found for the provided filters',
            'data'        => [],
        ], 200);
    }

    $responseData = $dragIcons->map(function ($dragIcon) {
        $icon     = $dragIcon->icon;
        $category = $icon?->category;

        $imagePath = null;
        if ($icon?->path) {
            try {
                $imagePath = str_starts_with($icon->path, 'http')
                    ? $icon->path
                    : \Storage::url($icon->path);
            } catch (\Exception $e) {
                \Log::error("Error generating URL for icon path: {$icon->path}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'drag_icon_id'   => $dragIcon->id,
            'icon_id'        => $icon?->id,
            'icon_name'      => $icon->name ?? 'Unknown',
            'icon_image_url' => $imagePath,
            'category_name'  => $category->category_name ?? 'No Category',
            'latitude'       => $dragIcon->latitude,
            'longitude'      => $dragIcon->longitude,
            'message'        => $dragIcon->message,
            'building_id'    => $dragIcon->building_id,
            'floor_id'       => $dragIcon->floor_id,
            'basement_id'    => $dragIcon->basement_id,
            'rotation'       => $dragIcon->rotation,
        ];
    })->values();

    return response()->json([
        'success'     => true,
        'status_code' => 200,
        'message'     => 'Drag Icons List',
        'data'        => $responseData,
    ], 200);
}


// ==========================React Api For Save Drag Icons============================
public function dragIconReactNSave(Request $request)
{
    $userId = Auth::id();

    // Check if user is authenticated
    if (!$userId) {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => 'User Not Authenticated',
            'data' => [],
        ]);
    }

    // Validation for required fields
    $validation = Validator::make($request->all(), [
        'building_id' => 'required|integer',
        'icon_id' => 'required|array',
        'latitude' => 'required|array',
        'longitude' => 'required|array',
        'icon_id.*' => 'required|integer',
        'latitude.*' => 'required|numeric',
        'longitude.*' => 'required|numeric',
        'message' => 'nullable|array'
    ]);

    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'status_code' => 400,
            'message' => $validation->errors()->first(),
            'data' => [],
        ]);
    }

    // Ensure all arrays are of equal length
    $count = count($request->icon_id);
    if (
        $count !== count($request->latitude) ||
        $count !== count($request->longitude)
    ) {
        return response()->json([
            'success' => false,
            'status_code' => 422,
            'message' => 'Mismatch in number of icon_id, latitude, and longitude values',
            'data' => [],
        ]);
    }

    $buildingId = $request->building_id;
    $floorId = $request->floor_id;
    $basementId = $request->basement_id;

    $savedIcons = [];

    for ($i = 0; $i < $count; $i++) {
        $savedIcons[] = DragIcon::create([
            'user_id' => $userId,
            'building_id' => $buildingId,
            'floor_id' => $floorId,
            'basement_id' => $basementId,
            'icon_id' => $request->icon_id[$i],
            'latitude' => $request->latitude[$i],
            'longitude' => $request->longitude[$i],
            'message'   =>  $request->message[$i],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return response()->json([
        'success' => true,
        'status_code' => 200,
        'message' => 'Icons Saved Successfully',
        'data' => $savedIcons,
    ]);
}




public function dragIconUpdate(Request $request)
{
    $userId = Auth::id();

    // Validation for either a single drag_icon_id or an array of drag_icon_ids
    $validation = Validator::make($request->all(), [
        'drag_icon_id' => 'required', // We need to validate this as either a single ID or an array
        'latitude' => 'required',
        'longitude' => 'required',
    ]);

    // Return validation error response
    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'status_code' => 400,
            'message' => $validation->errors()->first(),
            'data' => [],
        ]);
    }

    // Check if user is authenticated
    if (!$userId) {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => 'User Not Authenticated',
            'data' => [],
        ]);
    }

    // Get the drag_icon_id, it can be a single value or an array
    $dragIconIds = is_array($request->drag_icon_id) ? $request->drag_icon_id : [$request->drag_icon_id];

    // Find matching DragIcon records
    $icondrag = DragIcon::whereIn('id', $dragIconIds)->get();

    // Check if any matching DragIcon records are found
    if ($icondrag->isEmpty()) {
        return response()->json([
            'success' => false,
            'status_code' => 404,
            'message' => 'Drag Icon Not Found',
            'data' => [],
        ]);
    }

    // Update latitude and longitude for each record
    foreach ($icondrag as $icon) {
        $icon->latitude = $request->latitude;
        $icon->longitude = $request->longitude;
        $icon->save();
    }

    // Return success response
    return response()->json([
        'success' => true,
        'status_code' => 200,
        'message' => 'Data Updated Successfully',
        'data' => $icondrag,
    ]);
}



// ==========================Drag Icon Delete Api==============================
public function deleteDragIcon(Request $request)
{
    if (!Auth::check()) {
        return response()->json([
            'success'     => false,
            'status_code' => 401,
            'message'     => 'User not authenticated',
        ]);
    }

    $userId = Auth::id();

    $validation = Validator::make($request->all(), [
        'drag_icon_id' => 'required|integer',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'status_code' => 422,
            'message' => $validation->errors()->first(),
            'data' => [],
        ]);
    }

    DB::beginTransaction();

    try {
        $iconId = $request->drag_icon_id;
        $deleted = DragIcon::where('id', $iconId)
                           ->where('user_id', $userId)
                           ->delete();

        if ($deleted) {
            DB::commit();
            return response()->json([
                'success'     => true,
                'status_code' => 200,
                'message'     => 'Icon deleted successfully.',
            ]);
        } else {
            DB::rollback();
            return response()->json([
                'success'     => false,
                'status_code' => 404,
                'message'     => 'Icon not found or not owned by user.',
            ]);
        }

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'success'     => false,
            'status_code' => 500,
            'message'     => 'Something went wrong: ' . $e->getMessage(),
        ]);
    }
}


public function dragIconResize(Request $request)
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => 'Authentication Required',
        ]);
    }

    $validation = Validator::make($request->all(), [
        'icon_id' => 'required',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'status_code' => 400,
            'message' => $validation->errors()->first(),
            'data' => [],
        ]);
    }

    DB::beginTransaction();

    try {
        $dragIcon = new DragIconResize();
        $dragIcon->user_id = $userId;
        $dragIcon->icon_id = $request->icon_id;
        $dragIcon->building_id = $request->building_id;
        $dragIcon->basement_id = $request->basement_id;
        $dragIcon->floor_id = $request->floor_id;
        $dragIcon->icon_lat = $request->icon_lat;
        $dragIcon->icon_long = $request->icon_long;

        $dragIcon->point1_lat = $request->point1_lat;
        $dragIcon->pont1_long = $request->pont1_long;
        $dragIcon->point2_lat = $request->point2_lat;
        $dragIcon->pont2_long = $request->pont2_long;
        $dragIcon->point3_lat = $request->point3_lat;
        $dragIcon->pont3_long = $request->pont3_long;
        $dragIcon->point4_lat = $request->point4_lat;
        $dragIcon->pont4_long = $request->pont4_long;

        $dragIcon->save();
        DB::commit();

        return response()->json([
            'success' => true,
            'success_code' => 200,
            'message' => 'Data Stored Successfully',
            'data' => $dragIcon
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'status_code' => 500,
            'message' => 'Something went wrong: ' . $e->getMessage(),
        ]);
    }
}


// ======================Get Resize DragIcon========================
public function getResizeIcons(Request $request)
{  


    $userId = Auth::id();
    $user = Auth::user();

    if (!$userId) {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => 'User not authenticated',
        ], 401);
    }

    $request->validate([
        'building_id' => 'required|integer|exists:buildings,id',
    ]);

    $buildingId = $request->query('building_id');
    $floorId = $request->query('floor_id');
    $basementId = $request->query('basement_id');

    $query = DragIconResize::with(['icon.category', 'building', 'floor', 'basement'])
        ->where('building_id', $buildingId);

    if ($user->role == 'Editor') {
        $query->where('user_id', $userId);
    }

    if (!empty($floorId)) {
        $query->where('floor_id', $floorId);
    }

    if (!empty($basementId)) {
        $query->where('basement_id', $basementId);
    }

    $dragIcons = $query->get();

    if ($dragIcons->isEmpty()) {
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'No Drag Icons found for the provided filters',
            'data' => [],
        ], 200);
    }

    $responseData = $dragIcons->map(function ($item) {
        $icon = $item->icon;
        $category = $icon?->category;
        $imagePath = $icon ? str_replace('public/', 'storage/', $icon->path) : null;

        return [
            'drag_icon_id'     => $item->id,
            'icon_id'          => $icon?->id,
            'icon_name'        => $icon?->name ?? 'Unknown',
            'icon_image_url'   => $imagePath ? url($imagePath) : null,
            'category_name'    => $category?->category_name ?? 'No Category',
            'latitude'         => $item->icon_lat,
            'longitude'        => $item->icon_long,
            'rotation'         => $item->rotation,
            'message'          => $item->message,

            // Points
            'point1_lat'       => $item->point1_lat,
            'point1_long'      => $item->pont1_long,
            'point2_lat'       => $item->point2_lat,
            'point2_long'      => $item->pont2_long,
            'point3_lat'       => $item->point3_lat,
            'point3_long'      => $item->pont3_long,
            'point4_lat'       => $item->point4_lat,
            'point4_long'      => $item->pont4_long,

            // Related Building
            'building'         => [
                'id'      => $item->building?->id,
                'name'    => $item->building?->building_name,
                'address' => $item->building?->building_address,
            ],

            // Related Floor
            'floor'            => $item->floor ? [
                'id'     => $item->floor->id,
                'name'   => $item->floor->floor_name,
                'image'  => $item->floor->floor_image ? url('storage/' . $item->floor->floor_image) : null,
            ] : null,

            // Related Basement
            'basement'         => $item->basement ? [
                'id'     => $item->basement->id,
                'name'   => $item->basement->floor_name,
                'image'  => $item->basement->floor_image ? url('storage/' . $item->basement->floor_image) : null,
            ] : null,
        ];
    });

    return response()->json([
        'success' => true,
        'status_code' => 200,
        'message' => 'Drag Icons List',
        'data' => $responseData,
    ]);
}


public function deleteDragIconResize(Request $request)
{
    $iconId = $request->input('icon_id');
    $floorId = $request->input('floor_id');
    $basementId = $request->input('basement_id');

    if (!$iconId) {
        return response()->json([
            'success' => false,
            'message' => 'icon_id is required.'
        ], 400);
    }

    if (is_null($floorId) && is_null($basementId)) {
        return response()->json([
            'success' => false,
            'message' => 'Either floor_id or basement_id must be provided.'
        ], 400);
    }

    DB::beginTransaction();
    try {
        $query = DragIconResize::where('icon_id', $iconId);

        if (!is_null($floorId)) {
            $query->where('floor_id', $floorId)->whereNull('basement_id');
        } elseif (!is_null($basementId)) {
            $query->where('basement_id', $basementId)->whereNull('floor_id');
        }

        $deletedCount = $query->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => $deletedCount > 0 ? 'Icon resize entry deleted successfully.' : 'No matching record found.',
            'deleted_count' => $deletedCount
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error deleting entry: ' . $e->getMessage()
        ], 500);
    }
}
// =============================End========================================
}
