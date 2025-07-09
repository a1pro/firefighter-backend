<?php

namespace App\Http\Controllers\Admin;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Basement;
use App\Models\DefaultSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class BuildingController extends Controller
{
    // Store Building and Floor Data
 // Store Building and Floor Data
 public function store(Request $request)
{
    $userid = Auth::id();

    $buildingapproval = DefaultSetting::first();
    $buildingstatus = $buildingapproval->building_approval;
    $layoutstatus = $buildingstatus == 1 ? '1' : '0';

    $validated = $request->validate([
        'building_name' => 'required|string|max:255',
        'building_address' => 'required|string',
        'zipcode' => 'nullable|string|max:10',
        'total_basement' => 'nullable|integer|min:0',
        'floor_name' => 'nullable|array',
        'floor_name.*' => 'nullable|string|max:255',
        'floor_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'basement_name' => 'nullable|array',
        'basement_name.*' => 'nullable|string|max:255',
        'basement_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    try {
        $building = new Building();
        $building->user_id = $userid;
        $building->building_name = $request->building_name;
        $building->building_address = $request->building_address;
        $building->zipcode = $request->zipcode ?? NULL;
        $building->suite_number = $request->suite_number;
        $building->total_floor = $request->total_floor ?? 1;
        $building->total_basement = $request->total_basement ?? 0;
        $building->status = '1';
        $building->save();

        if (!empty($request->floor_name) && is_array($request->floor_name)) {
            foreach ($request->floor_name as $index => $floorName) {
                $floor = new Floor();
                $floor->building_id = $building->id;
                $floor->floor_name = $floorName;

                if ($request->hasFile('floor_image') && isset($request->file('floor_image')[$index])) {
                    $imagePath = $request->file('floor_image')[$index]->store('floor_images', 'public');
                    $floor->floor_image = str_replace('public/', '', $imagePath);
                    $floor->status = '1';
                }

                $floor->save();
            }
        }

        if (!empty($request->basement_name) && is_array($request->basement_name)) {
            foreach ($request->basement_name as $index => $basementName) {
                $basement = new Basement();
                $basement->building_id = $building->id;
                $basement->basement_name = $basementName;

                if ($request->hasFile('basement_image') && isset($request->file('basement_image')[$index])) {
                    $imagePath = $request->file('basement_image')[$index]->store('basement_images', 'public');
                    $basement->basement_image = str_replace('public/', '', $imagePath);
                    $basement->status = '1';
                }

                $basement->save();
            }
        }

        // Return JSON for AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Building created successfully.'
            ]);
        }

        // Standard redirect fallback
        return redirect()->route('admin.buildings.index')->with('success', 'Building added successfully');
        
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

 
public function updateStatus(Request $request, $buildingId)
{
    $building = Building::findOrFail($buildingId);
    $building->status = $request->status;
    $building->save();

    return response()->json(['success' => true]);
}
public function index()
{
    // Retrieve all buildings with their related floors and basements.
    $buildings = Building::with(['floors', 'basements'])
        ->orderBy('id', 'desc')
        ->get();  // Retrieve buildings with all related data

    return view('admin.buildings.index', compact('buildings'));
}






 // Show the form for editing the specified building
 public function edit(Building $building)
 {
     // Pass the building data to the edit view
     return view('admin.buildings.edit', compact('building'));
 }

 // Update the building and floors
 public function update(Request $request, $id)
{
    try {
        $building = Building::findOrFail($id);

        $building->building_name = $request->building_name;
        $building->suite_number = $request->suite_number;
        $building->building_address = $request->building_address;
        $building->zipcode = $request->zipcode;
        $building->total_floor = $request->total_floor;

        // Floors
        if ($request->has('floor_name')) {
            foreach ($request->floor_name as $index => $floorName) {
                $floorId = $request->floor_id[$index] ?? null;
                $floor = Floor::findOrNew($floorId);
                $floor->floor_name = $floorName;

                if ($request->hasFile("floor_image.$index")) {
                    $floor->floor_image = $request->file("floor_image.$index")->store('floor_images', 'public');
                }

                $building->floors()->save($floor);
            }
        }

        // Basements
        if ($request->has('basement_name')) {
            foreach ($request->basement_name as $index => $basementName) {
                $basementId = $request->basement_id[$index] ?? null;
                $basement = Basement::findOrNew($basementId);
                $basement->basement_name = $basementName;

                if ($request->hasFile("basement_image.$index")) {
                    $basement->basement_image = $request->file("basement_image.$index")->store('basement_images', 'public');
                }

                $building->basements()->save($basement);
            }
        }

        $building->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Building updated successfully.']);
        }

        return redirect()->route('admin.buildings.index')->with('success', 'Building updated successfully.');

    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return back()->with('error', 'Update failed: ' . $e->getMessage());
    }
}

 // Delete the building and associated floors
 public function destroy(Building $building)
 {
     // Delete floors associated with the building
     $building->floors()->delete();
     // Delete the building itself
     $building->delete();

     // Redirect back to the buildings index page with a success message
     return redirect()->route('admin.buildings.index')->with('success', 'Building deleted successfully');
 }
}
