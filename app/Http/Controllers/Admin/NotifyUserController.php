<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\AreaAlloted;
use Yajra\DataTables\Facades\DataTables;
use App\Models\UsCounty;
use DB;


  

use Illuminate\Support\Facades\Hash;
class NotifyUserController extends Controller
{
    public function index()
    {
    $users = User::where('role','Notify')->get();
    return view('admin.notifyuser.index',compact('users'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
 
            'first_name'    => 'required',
            'email'         => 'required|email|unique:users,email',
            'phone_number'  => 'required|unique:users,phone_number',
             
        ]);

        $data = $request->all();
        $notifyuser                 =       new User();
        $notifyuser->first_name     =   $request->input('first_name');
        $notifyuser->last_name      =   $request->input('last_name');
        $notifyuser->email          =   $request->input('email');
        $notifyuser->phone_number   =   $request->input('phone_number');
        $notifyuser->password       =   Hash::make('123456');
        $notifyuser->role           =   'Notify';
        $notifyuser->save();
        return response()->json(['success' => true, 'notifyuser' => $notifyuser]);
    }
      

    public function edit($id)
    {
        $notifyuser = User::findOrFail($id);
        return response()->json(['success' => true, 'notifyuser' => $notifyuser]);
    }

    // Update method to update a category
    public function update(Request $request, $id)
{
    $request->validate([
        'first_name'   => 'required',
        'last_name'    => 'required',
        'email'        => 'required|email|unique:users,email,' . $id,
        'phone_number' => 'required|unique:users,phone_number,' . $id,
    ]);

    $notifyuser = User::findOrFail($id);
    $notifyuser->update([
        'first_name'   => $request->first_name,
        'last_name'    => $request->last_name,
        'email'        => $request->email,
        'phone_number' => $request->phone_number,
    ]);

    return response()->json(['success' => true, 'notifyuser' => $notifyuser]);
}

public function destroy($id)
{
    $notifyuser = User::findOrFail($id);
    $notifyuser->delete();

    return response()->json(['success' => true, 'message' => 'Notify User deleted successfully.']);
}

// ----------------------------------------------------------------------------

public function storeArea(Request $request) {
   
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
            'state'   => 'required|exists:states,id',
            'county'  => 'required',
            'city' => 'required|exists:cities,id',
            'pincode' => 'required|string|max:10',
    
        ]);
        $state= $data['state'];
        $county = $data['county'];
        $city =  $data['city'];
        $pincode =  $data['pincode'];
    }
    $area = new AreaAlloted();
   $area->user_id = $request->user_id;
   $area->country = $request->country;
   $area->state = $state;
   $area->city = $city;
   $area->county  = $county;
   $area->pincode = $pincode;
   $area->save();

    return response()->json(['success' => true]);
}

// ----------------------------------------------------------------------------

//--------------------------------------------------------------------------
public function getCountries() {
    return response()->json(['countries' => Country::all()]);
}

public function getStates($countryId) {
    return response()->json(['states' => State::where('country_id', $countryId)->get()]);
}



public function getCounty($stateName)
{
    
    return response()->json(['counties' => UsCounty::where('state_name', $stateName)->get()]);
}


public function getCities($stateId) {
    return response()->json(['cities' => City::where('state_id', $stateId)->get()]);
}
// --------------------------------------------------------------------------

// -----------------------Get Notify User Lising------------------------------
public function assign()
{
    // Return the view and pass any necessary data
    return view('admin.notifyuser.areassignment');
}

public function getUsersData(Request $request)
{
    // Fetch users with relationships for country, state, city, and area
    $users = User::select(
        'users.id',
        'users.first_name',
        'users.phone_number',
        'area_alloted.id as area_id',
        'area_alloted.area',
        'area_alloted.pincode',
        'cities.name as city_name',
        'states.name as state_name',
        'uscounties.county as county_name',
        'search_countries.short_name as country_name',
        DB::raw("CASE 
                    WHEN area_alloted.city = 'ALL' AND area_alloted.state = 'ALL' AND area_alloted.county  THEN 'All'
                    WHEN states.id IS NULL THEN 'All'
                    ELSE states.name 
                 END as state_name"),
        DB::raw("CASE 
                    WHEN area_alloted.city = 'ALL' AND area_alloted.state = 'ALL' AND area_alloted.county  THEN 'All'
                    WHEN cities.id IS NULL THEN 'All'
                    ELSE cities.name 
                 END as city_name"),
                 DB::raw("CASE 
                 WHEN area_alloted.city = 'ALL' AND area_alloted.state = 'ALL' AND area_alloted.county THEN 'All'
                 WHEN uscounties.id IS NULL THEN 'All'
                 ELSE uscounties.county 
              END as county_name"),
    )
    ->join('area_alloted', 'area_alloted.user_id', '=', 'users.id')
    ->leftJoin('cities', 'cities.id', '=', 'area_alloted.city') // LEFT JOIN for cities
    ->leftJoin('states', 'states.id', '=', 'area_alloted.state') // LEFT JOIN for states
    ->leftJoin('uscounties', 'uscounties.id', '=', 'area_alloted.county') 
    ->leftJoin('search_countries', 'search_countries.id', '=', 'area_alloted.country')
    ->where('users.role', 'Editor') // Only fetch Editors
    ->where(function($query) {
        // Include rows where city and state are NOT 'ALL'
        $query->where('area_alloted.city', '!=', 'ALL')
              ->where('area_alloted.state', '!=', 'ALL')
              ->where('area_alloted.county', '!=', 'ALL');
    })
    ->orWhere(function($query) {
        // Include rows where city and state ARE 'ALL'
        $query->where('area_alloted.city', '=', 'ALL')
              ->where('area_alloted.state', '=', 'ALL')
              ->where('area_alloted.county', '=', 'ALL');
    })
    ->orderBy('users.id', 'desc')
    ->get();
    // Return the data as a DataTables response
    return DataTables::of($users)
        ->make(true);
}


public function editarea($id)
{
    // Fetch the user information from the database with the joins as required
    $user = User::select(
        'users.id',
        'users.first_name',
        'users.phone_number',
        'area_alloted.id as areaId',
        'area_alloted.area',
        'area_alloted.pincode',
        'cities.id as city_id',
        'states.id as state_id',
        'search_countries.id as country_id',
        'cities.name as city_name',
        'states.name as state_name',
        'search_countries.short_name as country_name'
    )
    ->join('area_alloted', 'area_alloted.user_id', '=', 'users.id')
    ->join('cities', 'cities.id', '=', 'area_alloted.city')
    ->join('states', 'states.id', '=', 'cities.state_id')
    ->join('search_countries', 'search_countries.id', '=', 'states.country_id')
    ->where('area_alloted.id', $id)
    ->first(); // Use `first()` to get a single row as you are editing a specific user
    
    if ($user) {
        $countries = Country::all(); // Assuming you have a `Country` model
        $states = State::where('country_id', $user->country_id)->get(); // States by Country
        $cities = City::where('state_id', $user->state_id)->get(); // Cities by State

        return view('admin.notifyuser.editassignment', compact('user', 'countries', 'states', 'cities'));
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
    $assignment = AreaAlloted::where('id', $id)->first();

    if ($assignment) {
        // Directly update the assignment values
        $assignment->country = $request->country;
        $assignment->state= $request->state;
        $assignment->city = $request->city;
        $assignment->area = $request->area;
        $assignment->pincode = $request->pincode;
        $assignment->save(); // Save the updated record in the database

        // Redirect to the assignment list route with a success message
        return redirect()->route('notify.users.area')->with('success', 'Editor assignment updated successfully!');
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

public function getCitiesByState(Request $request)
{
    $cities = City::where('state_id', $request->state_id)->get();
    return response()->json(['cities' => $cities]);
}




    public function destroyNotify(Request $request)
    {
       
    // Find the specific area record to delete using the provided ID
    $assignArea = AreaAlloted::find($request->id);
    
    if ($assignArea) {
      
        $assignArea->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false]);
    }

//--------------------------Notify User Listing End---------------------------


}
