<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AreaAlloted;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\UsCounty;
use App\Models\EditorAssignArea;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
class EditorController extends Controller
{
    // Display the editor assignment page
    public function index()
    {
        // Fetch users who are 'Notify' role
        $users = User::where('role', 'Editor')->get();
        return view('admin.editor.index', compact('users'));
    }

    // Fetch users for DataTables (with area information)

    public function assign()
    {
        return view('admin.editor.editorassignarea');
    }

    // Fetch users with their related area, country, state, and city details
    public function getUsersData(Request $request)
    {
        // Fetch users with the necessary relationships and data
        $users = User::select(
            'users.id',
            'users.first_name',
            'users.phone_number',
            'editor_assign_area.id as area_id',
            'editor_assign_area.area',
            'editor_assign_area.pincode',
            'cities.name as city_name',
            'states.name as state_name',
            'search_countries.short_name as country_name',
            'editor_assign_area.county as county_ids', // Fetch the comma-separated county IDs
            DB::raw("CASE 
                        WHEN editor_assign_area.city = 'ALL' AND editor_assign_area.state = 'ALL' AND editor_assign_area.county THEN 'All'
                        WHEN states.id IS NULL THEN 'All'
                        ELSE states.name 
                     END as state_name"),
            DB::raw("CASE 
                        WHEN editor_assign_area.city = 'ALL' AND editor_assign_area.state = 'ALL' AND editor_assign_area.county THEN 'All'
                        WHEN cities.id IS NULL THEN 'All'
                        ELSE cities.name 
                     END as city_name")
        )
        ->join('editor_assign_area', 'editor_assign_area.user_id', '=', 'users.id')
        ->leftJoin('cities', 'cities.id', '=', 'editor_assign_area.city') // LEFT JOIN for cities
        ->leftJoin('states', 'states.id', '=', 'editor_assign_area.state') // LEFT JOIN for states
        ->leftJoin('search_countries', 'search_countries.id', '=', 'editor_assign_area.country')
        ->leftJoin('uscounties', 'uscounties.id', '=', 'editor_assign_area.county') // Left Join to get county name
        ->where('users.role', 'Editor') // Only fetch Editors
        ->where(function($query) {
            // Include rows where city and state are NOT 'ALL'
            $query->where('editor_assign_area.city', '!=', 'ALL')
                  ->where('editor_assign_area.state', '!=', 'ALL')
                  ->where('editor_assign_area.county', '!=', 'ALL');
        })
        ->orWhere(function($query) {
            // Include rows where city and state ARE 'ALL'
            $query->where('editor_assign_area.city', '=', 'ALL')
                  ->where('editor_assign_area.state', '=', 'ALL')
                  ->where('editor_assign_area.county', '=', 'ALL');
        })
        ->orderBy('users.id', 'desc')
        ->get();
    
        // Process county IDs to fetch county names
        $users = $users->map(function($user) {
            if ($user->county_ids) {
                // If there are multiple counties (comma-separated IDs)
                $countyIds = explode(',', $user->county_ids); // Split the county IDs
                $countyNames = [];
    
                // Fetch the names for each county ID
                foreach ($countyIds as $countyId) {
                    $county = \App\Models\UsCounty::find($countyId); // Assuming `UsCounty` is the model for counties
                    if ($county) {
                        $countyNames[] = $county->county; // Add the county name to the array
                    }
                }
    
                // Join the county names into a comma-separated string
                $user->county_names = implode(', ', $countyNames);
            } else {
                $user->county_names = 'All'; // If no counties are assigned, set to 'All'
            }
    
            return $user;
        });
    
        // Return the data as a DataTables response
        return DataTables::of($users)
            ->make(true);
    }
    

    // Store the area for the user
    public function storeArea(Request $request)
    {
        $data = $request->all();
        if($data['state']=='all')
        {
            $validated = $request->validate([
                'country' => 'required|exists:search_countries,id',
              
            ]); 
            $state = 'All';
            $county = 'All';
            $city = 'All';
            $pincode = 'All';
             
        }
        else{
        $validated = $request->validate([
            'country' => 'required|exists:search_countries,id',
       
        ]);
        $state= $data['state'];
               $county = implode(',', $data['county']);
        $city =  $data['city'];
        $pincode = $data['pincode'];
       
    }
        // Create and save the new area assignment
        $area = new EditorAssignArea();
        $area->user_id = $request->user_id;
        $area->country = $request->country;
        $area->state = $state;
        $area->county  = $county;
        $area->city = $city;
        $area->area = $request->area;
        $area->pincode = $pincode;
        $area->save();

        return response()->json(['success' => true]);
    }

    // Fetch all countries
    public function getCountries()
{
    return response()->json(['countries' => Country::all()]);
}


    // Fetch states based on selected country
    public function getStates($countryId)
    {
        return response()->json(['states' => State::where('country_id', $countryId)->get()]);
    }
    
    public function getCounty($stateName)
    {
        
        return response()->json(['counties' => UsCounty::where('state_name', $stateName)->get()]);
    }
    

    // Fetch cities based on selected state
    public function getCities($stateId)
    {
        return response()->json(['cities' => City::where('state_id', $stateId)->get()]);
    }
    
   
 

    public function editarea($id)
{
    $user = User::select(
        'users.id',
        'users.first_name',
        'users.phone_number',
        'editor_assign_area.id as area_id',
        'editor_assign_area.area',
        'editor_assign_area.pincode',
        'cities.name as city_name',
        'states.name as state_name',
        'search_countries.short_name as country_name',
        'search_countries.id as country_id',
        DB::raw("CASE 
                    WHEN editor_assign_area.city = 'ALL' AND editor_assign_area.state = 'ALL' THEN 'All'
                    WHEN states.id IS NULL THEN 'All'
                    ELSE states.name 
                 END as state_name"),
        DB::raw("CASE 
                    WHEN editor_assign_area.city = 'ALL' AND editor_assign_area.state = 'ALL' THEN 'All'
                    WHEN cities.id IS NULL THEN 'All'
                    ELSE cities.name 
                 END as city_name")
    )
    ->join('editor_assign_area', 'editor_assign_area.user_id', '=', 'users.id')
    ->leftJoin('cities', 'cities.id', '=', 'editor_assign_area.city') // LEFT JOIN for cities
    ->leftJoin('states', 'states.id', '=', 'editor_assign_area.state') // LEFT JOIN for states
    ->leftJoin('search_countries', 'search_countries.id', '=', 'editor_assign_area.country')
    ->where('users.role', 'Editor') // Only fetch Editors
    ->where(function($query) {
        // Include rows where city and state are NOT 'ALL'
        $query->where('editor_assign_area.city', '!=', 'ALL')
              ->where('editor_assign_area.state', '!=', 'ALL');
    })
    ->orWhere(function($query) {
        // Include rows where city and state ARE 'ALL'
        $query->where('editor_assign_area.city', '=', 'ALL')
              ->where('editor_assign_area.state', '=', 'ALL');
    })
    ->orderBy('users.id', 'desc')
    ->where('editor_assign_area.id', $id)
    ->get(); // Use `first()` to get a single row as you are editing a specific user
    
    if ($user) {
        $countries = Country::all(); // Assuming you have a `Country` model
        $states = State::where('country_id', $user->country_id)->get(); // States by Country
        $cities = City::where('state_id', $user->state_id)->get(); // Cities by State

        return view('admin.editor.editarea', compact('user', 'countries', 'states', 'cities'));
    } else {
        abort(404); // Or redirect to a not found page
    }
}

public function updateArea(Request $request, $id)
{
    // Validate the incoming data
        
    $validatedData = $request->validate([
        'country' => 'required',
        'state' => 'required',
        'city' => 'required',
        'area' => 'required|string|max:255',
        'pincode' => 'required|string|max:10',
    ]);

    // Find the existing editor assignment (assuming user_id is unique per user)
    $assignment = EditorAssignArea::where('id', $id)->first();

    if ($assignment) {
        // Directly update the assignment values
        $assignment->country = $request->country;
        $assignment->state= $request->state;
        $assignment->city = $request->city;
        $assignment->area = $request->area;
        $assignment->pincode = $request->pincode;
        $assignment->save(); // Save the updated record in the database

        // Redirect to the assignment list route with a success message
        return redirect()->to('admin/editor/users/area')->with('success', 'Operation successful!');

    } else {
        // If no assignment exists, show an error
        return back()->with('error', 'Editor assignment not found.');
    }
}



    public function getStatesByCountry(Request $request)
{
    $states = State::where('country_id', $request->country_id)->get();
    return response()->json(['states' => $states]);
}


public function getCountyByStateName(Request $request)
{
    $county = UsCounty::where('state_name', $request->state_name)->get();
    return response()->json(['county' => $county]);
}



public function getCitiesByState(Request $request)
{
    $cities = City::where('state_id', $request->state_id)->get();
    return response()->json(['cities' => $cities]);
}


public function destroy(Request $request)
{
    // Validate the incoming request to ensure the 'id' is provided
    

    // Find the specific area record to delete using the provided ID
    $assignArea = EditorAssignArea::find($request->id);
    
    if ($assignArea) {
        // Delete the area assignment record
        $assignArea->delete();
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Record not found.']);
}


    
}
