<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuildingLayout;
use App\Models\FloorImage;
class BuildingLayoutcontroller extends Controller
{
    public function index()
    {
        // Eager load floor images
        $buildingLayouts = BuildingLayout::with('floorImages')->get();
        return view('admin.buildinglayout.index', compact('buildingLayouts'));
    }

    public function show($id)
{
    $layout = BuildingLayout::with('floorImages')->findOrFail($id);
    return response()->json($layout);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'number_of_floors' => 'required|integer|min:1',
            'floor_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Save building layout
        $layout = BuildingLayout::create([
            'building_name' => $request->building_name,
            'address' => $request->address,
            'number_of_floors' => $request->number_of_floors
        ]);
        
        if ($request->hasFile('floor_images')) {
            foreach ($request->file('floor_images') as $index => $file) {
                $path = $file->store('floor_images', 'public');
                $layout->floorImages()->create([
                    'floor_number' => $index + 1,
                    'image_path' => $path
                ]);
            }
        }
        
        // Return the layout along with images as part of the response
        return response()->json([
            'success' => true,
            'layout' => $layout,
            'floor_images' => $layout->floorImages // Include the floor images
        ]);
        
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'number_of_floors' => 'required|integer|min:1',
            'floor_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $layout = BuildingLayout::findOrFail($id);
        $layout->building_name = $request->building_name;
        $layout->address = $request->address;
        $layout->number_of_floors = $request->number_of_floors;
        $layout->save();
    
        // Handle floor images
        $existingFloorImages = $request->input('existing_floor_images', []);
        $newFloorImages = $request->file('floor_images', []);
    
        // Remove images that are no longer associated with a floor (if any)
        $layout->floorImages()->delete();
    
        // Store new floor images or retain existing ones
        foreach ($newFloorImages as $index => $file) {
            $path = $file->store('floor_images', 'public');
            $layout->floorImages()->create([
                'floor_number' => $index + 1,
                'image_path' => $path,
            ]);
        }
    
        // Store existing floor images if no new file is uploaded for a floor
        foreach ($existingFloorImages as $index => $imageId) {
            if ($imageId) {
                $layout->floorImages()->create([
                    'floor_number' => $index + 1,
                    'image_id' => $imageId, // Store the existing image ID
                ]);
            }
        }
    
        return response()->json([
            'success' => true,
            'layout' => $layout,
        ]);
    }
    
    public function destroy($id)
    {

        $layout = BuildingLayout::findOrFail($id);
        
        $layout->delete();

        return response()->json(['success' => true]);
    }
}
