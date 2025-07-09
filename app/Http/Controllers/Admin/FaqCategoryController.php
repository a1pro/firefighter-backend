<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaqCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;



class FaqCategoryController extends Controller
{
    public function index()
    {
        $icons = FaqCategory::all();
        return view('admin.faqcategory.index', compact('icons'));
    }
    
    // Store method to handle the creation of a new category
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'category_name' => 'required|string|max:255',
          
        ]);
    
        // Handle file upload if image is present
        $imagePath = null;
         
    
        // Create the new category
        $category = FaqCategory::create([
            'category_name' => $request->category_name,
            
        ]);
    
        // If the request is an AJAX request, return a JSON response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'category_name' => $category->category_name,
                    'image_url' => $imagePath ? $this->getImageUrl($imagePath) : null, // Use getImageUrl helper method
                ]
            ]);
        }
    
        // Redirect with success message
        return redirect()->route('admin.faqCategories.index')->with('success', 'Category created successfully');
    }
    
    // Edit method to fetch a category for editing
    public function edit($id)
    {
        $category = FaqCategory::findOrFail($id);
        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'category_name' => $category->category_name,
                'image_url' => $category->category_image ? $this->getImageUrl($category->category_image) : null, // Use getImageUrl helper method
            ]
        ]);
    }
    
    // Update method to update a category
    public function update(Request $request, $id)
    {
        $category = FaqCategory::findOrFail($id);
    
        // Validate the incoming request
        $request->validate([
            'category_name' => 'required|string|max:255',
            'category_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
        ]);
    
        // Handle image upload if present
        if ($request->hasFile('category_image')) {
            // Delete the old image if it exists
            if ($category->category_image && Storage::exists('public/' . $category->category_image)) {
                Storage::delete('public/' . $category->category_image);
            }
    
            // Store the new image
            $file = $request->file('category_image');
            $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $category->category_image = $file->storeAs('public/uploads/categories', $fileName);
        }
    
        // Update the category name
        $category->category_name = $request->category_name;
        $category->save();
    
        // Return the updated category data as JSON
        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'category_name' => $category->category_name,
                'image_url' => $category->category_image ? $this->getImageUrl($category->category_image) : null, // Use getImageUrl helper method
            ]
        ]);
    }
    
    // Destroy method to delete a category
    public function destroy($id)
    {
        $category = FaqCategory::findOrFail($id);
    
        // Delete the category image if it exists
        if ($category->category_image && Storage::exists('public/' . $category->category_image)) {
            Storage::delete('public/' . $category->category_image);
        }
    
        // Delete the category
        $category->delete();
    
        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }
    
    // Helper function to get the correct image URL without public/
    private function getImageUrl($imagePath)
    {
        // Remove the 'public/' part from the path and return the correct asset URL
        return asset('storage/' . str_replace('public/', '', $imagePath));
    }
}
