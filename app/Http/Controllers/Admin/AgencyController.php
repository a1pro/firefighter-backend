<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;

class AgencyController extends Controller
{
    public function index()
    {
        $agency = Agency::all();
        return view('admin.agency.index', compact('agency'));
    }

    // Store method to handle the creation of new agency
    public function store(Request $request)
    {
        $request->validate([
            'agency_name' => 'required|string|max:255',
        ]);

        // Create a new agency
        $agency = Agency::create([
            'name' => $request->agency_name,
        ]);

        return response()->json(['success' => true, 'category' => $agency]);
    }

    // Edit method to fetch an agency for editing
    public function edit($id)
    {
        $agency = Agency::findOrFail($id);
        return response()->json(['success' => true, 'category' => $agency]);
    }

    // Update method to update an agency
    public function update(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);
        $agency->update([
            'name' => $request->agency_name,
        ]);

        return response()->json(['success' => true, 'category' => $agency]);
    }

    // Destroy method to delete an agency
    public function destroy($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->delete();

        return response()->json(['success' => true, 'message' => 'Agency deleted successfully.']);
    }
}
