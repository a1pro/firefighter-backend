<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StandardIcon;
use App\Models\IconCategory;
use App\Models\StandardIconCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StandardIconController extends Controller
{
    // Display all icons
    public function index()
    {
        $icons = StandardIcon::all();
        $iconcategory =StandardIconCategory::all();
        
        return view('admin.standardicon.index', compact('icons','iconcategory'));
    }

    // Store the uploaded icon
    public function store(Request $request) {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
            'category_id' => 'required',
        ]);
    
        // Handle file upload
     
$file = $request->file('icon');
$fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
 $path = $file->storeAs('public/uploads/standardicons', $fileName); 
 
// This stores in storage/app/public/uploads/standardicons

// You do not need to modify this part; Storage::url() will handle the correct public path.



        // Fetch the category name
        $category = StandardIconCategory::find($request->category_id); // Assuming 'category_id' is the ID of the category
        $categoryName = $category ? $category->category_name : null;
    
        // Create a new icon record in the database
        $icon = StandardIcon::create([
            'name' => $request->name,
            'path' => $path,
            'category_id' => $request->category_id,
        ]);
    
        // Return response for AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'icon' => [
                    'id' => $icon->id,
                    'name' => $icon->name,
                    'path' => asset('storage/' . $icon->path),
                    'category_name' => $categoryName,
                ]
            ]);
        }
    
        return redirect()->route('admin.standardicon.index')->with('success', 'Icon uploaded successfully');
    }
    
    
    // Edit method (for fetching icon details to edit)
    public function edit($id)
    {
      
        $icon = StandardIcon::find($id);
  
        if ($icon) {
            return response()->json([
                'success' => true,
                'icon' => $icon
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Icon not found.'
            ]);
        }
    }
    

    // Update the icon
    public function update(Request $request, $id)
    {
     
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
        ]);
    
        $icon = StandardIcon::findOrFail($id);
    
        // If a new file is uploaded, update the icon file
        if ($request->hasFile('icon')) {
            // Delete the old file from storage if it exists
            if ($icon->path && Storage::exists($icon->path)) {
                Storage::delete($icon->path);
            }
    
            $file = $request->file('icon');
            $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads/standardicons', $fileName);
            $icon->path = $path;  // Update the icon's file path
        }
    
        // Update the icon's name
        $icon->name = $request->name;
        $icon->save();
    
        // Return AJAX response with updated icon information
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'icon' => [
                    'id' => $icon->id,
                    'name' => $icon->name,
                    'path' => asset('storage/' . $icon->path),  // Full URL to the icon image
                ]
            ]);
        }
    
        // If not an AJAX request, redirect
        return redirect()->route('admin.standardicon.index')->with('success', 'Icon updated successfully');
    }
    

    // Destroy the icon
    public function destroy($id)
    {
        $icon = StandardIcon::findOrFail($id);

        // Delete the icon file from storage
        Storage::delete($icon->path);

        // Delete the icon record from the database
        $icon->delete();

        // Return AJAX response
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Icon deleted successfully',
                'id' => $id,
            ]);
        }

        return redirect()->route('admin.standardicon.index')->with('success', 'Icon deleted successfully');
    }
}


?>