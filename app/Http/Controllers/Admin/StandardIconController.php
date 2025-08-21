<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StandardIcon;
use App\Models\StandardIconCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StandardIconController extends Controller
{
    // Display all icons with search and pagination
    public function index(Request $request)
    {
        $query = StandardIcon::with('category');
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Category filter
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }
        
        // Handle pagination limit
        $perPage = $request->get('per_page', 10);
        
        if ($perPage === 'all') {
            $icons = $query->get();
            $icons = new \Illuminate\Pagination\LengthAwarePaginator(
                $icons, 
                $icons->count(), 
                $icons->count(), 
                1
            );
        } else {
            $icons = $query->latest()->paginate($perPage);
        }
        
        $iconcategory = StandardIconCategory::all();
        
        // If AJAX request, return JSON response with icons table HTML
        if ($request->ajax()) {
            $icons_html = view('admin.standardicon.icons_table', compact('icons'))->render();
            $pagination_html = $icons->appends(['per_page' => $perPage])->links()->toHtml();
            
            return response()->json([
                'success' => true,
                'icons_html' => $icons_html,
                'pagination_html' => $pagination_html,
                'icons_count' => $icons->count(),
                'total' => $icons->total(),
                'from' => $icons->firstItem(),
                'to' => $icons->lastItem()
            ]);
        }
        
        return view('admin.standardicon.index', compact('icons', 'iconcategory'));
    }

    // Store the uploaded icon
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon' => 'required|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Handle file upload
            $file = $request->file('icon');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads/standardicons', $fileName);

            // Create a new icon record in the database
            $icon = StandardIcon::create([
                'name' => $request->name,
                'path' => $path,
                'category_id' => $request->category_id,
            ]);

            // Get category name
            $categoryName = StandardIconCategory::find($request->category_id)->category_name;

            // Return response for AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Icon uploaded successfully',
                    'icon' => [
                        'id' => $icon->id,
                        'name' => $icon->name,
                        'path' => Storage::url($icon->path),
                    ],
                    'category_name' => $categoryName
                ]);
            }

            return redirect()->route('admin.standardicon.index')->with('success', 'Icon uploaded successfully');
        } catch (\Exception $e) {
            Log::error('Error storing icon: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error uploading icon: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error uploading icon: ' . $e->getMessage())->withInput();
        }
    }

    // Edit method (for fetching icon details to edit)
    public function edit($id)
    {
        try {
            $icon = StandardIcon::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'icon' => $icon
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching icon: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Icon not found.'
            ], 404);
        }
    }

    // Update the icon
    public function update(Request $request, $id)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $icon = StandardIcon::findOrFail($id);

            // If a new file is uploaded, update the icon file
            if ($request->hasFile('icon')) {
                // Delete the old file from storage if it exists
                if ($icon->path && Storage::exists($icon->path)) {
                    Storage::delete($icon->path);
                }

                $file = $request->file('icon');
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/uploads/standardicons', $fileName);
                $icon->path = $path;
            }

            // Update the icon's data
            $icon->name = $request->name;
            $icon->category_id = $request->category_id;
            $icon->save();

            // Get category name
            $categoryName = StandardIconCategory::find($request->category_id)->category_name;

            // Return AJAX response with updated icon information
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Icon updated successfully',
                    'icon' => [
                        'id' => $icon->id,
                        'name' => $icon->name,
                        'path' => Storage::url($icon->path),
                    ],
                    'category_name' => $categoryName
                ]);
            }

            return redirect()->route('admin.standardicon.index')->with('success', 'Icon updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating icon: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating icon: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating icon: ' . $e->getMessage())->withInput();
        }
    }

    // Destroy the icon
    public function destroy($id)
    {
        try {
            $icon = StandardIcon::findOrFail($id);

            // Delete the file from storage if it exists
            if (!empty($icon->path) && Storage::exists($icon->path)) {
                Storage::delete($icon->path);
            }

            $icon->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Icon deleted successfully',
                    'id' => $id
                ]);
            }

            return redirect()
                ->route('admin.standardicon.index')
                ->with('success', 'Icon deleted successfully');
        } catch (\Exception $e) {
            Log::error("Delete failed for icon ID {$id}: " . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting the icon: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error deleting the icon: ' . $e->getMessage());
        }
    }
}