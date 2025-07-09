<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\FaqCategory;

class FaqController extends Controller
{
    public function index()
    {
        $icons = Faq::all();
        $iconcategory = FaqCategory::all();
        
        return view('admin.Faq.index', compact('icons', 'iconcategory'));
    }

    // Store the question and answer (no image)
    public function store(Request $request) 
    {
        // Validate the incoming request
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',  // No max length for answer since it can be long
            'category_id' => 'required',
        ]);

        // Fetch the category name
        $category = FaqCategory::find($request->category_id);
        $categoryName = $category ? $category->category_name : null;

        // Create a new FAQ record in the database
        $faq = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'category_id' => $request->category_id,
        ]);

        // Return response for AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'faq' => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category_name' => $categoryName,
                ]
            ]);
        }

        return redirect()->route('admin.Faq.index')->with('success', 'FAQ added successfully');
    }

    // Edit FAQ details
    public function edit($id)
    {
        $faq = Faq::find($id);

        if ($faq) {
            return response()->json([
                'success' => true,
                'faq' => $faq
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'FAQ not found.'
            ]);
        }
    }

    // Update the FAQ (no image handling)
    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',  // No max length for answer since it can be long
        ]);

        $faq = Faq::findOrFail($id);

        // Update FAQ details
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->category_id = $request->category_id;
        $faq->save();

        // Return AJAX response with updated FAQ information
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'faq' => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ]
            ]);
        }

        return redirect()->route('admin.Faq.index')->with('success', 'FAQ updated successfully');
    }

    // Destroy the FAQ
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);

        // Delete the FAQ record from the database
        $faq->delete();

        // Return AJAX response
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'FAQ deleted successfully',
                'id' => $id,
            ]);
        }

        return redirect()->back()->with('success', 'FAQ deleted successfully');
    }
}
