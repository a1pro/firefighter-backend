<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\ContactMessage;
use App\Models\CourierSummary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Building;
use App\Models\StandardIcon;
class AdminController extends Controller
{

// ========================Index Page=============================
    public function dashboard(Request $request)
    {
        // Default filters, we fetch all users by default, no need to check for role here
        $total_users = User::whereIn('role', ['Viewer', 'Editor'])->count();
        
        // Fetch total buildings and icons
        $buildings = Building::count();
        $icons = StandardIcon::count();
    
        // Fetch all users with role 'Viewer' or 'Editor'
        $users = User::select('id', 'first_name', 'last_name', 'user_name', 'email', 'profile_photo', 'phone_number', 'role')
                     ->whereIn('role', ['Viewer', 'Editor'])
                     ->get();
    
        return view('admin.dashboard', compact('total_users', 'buildings', 'icons', 'users'));
    }

// ========================================End Page=======================================



public function getUsersData(Request $request)
{
    // Fetch users with their related agency name, handling the case where there's no agency
    $users = User::select('users.id', 'users.first_name', 'users.last_name', 'users.user_name', 'users.email', 'users.profile_photo', 'users.phone_number', 'users.role', 'agency.name as agency_name')
        ->leftJoin('agency', 'agency.id', '=', 'users.agency_id') // Use leftJoin to include users without agency
        ->whereIn('users.role', ['Viewer', 'Editor'])
        ->orderBy('users.id', 'desc')
        ->get();

    // Return DataTables response
    return DataTables::of($users)
        ->addColumn('agency_name', function ($user) {
            // Check if there's an associated agency, otherwise return "No Agency"
            return $user->agency_name ?? 'No Agency';
        })
        ->addColumn('action', function ($user) {
            return '<button class="btn btn-info">Edit</button>';
        })
        ->make(true);
}


// ==========================User Edit Form===============================================

// UserController.php

 

public function getUserDetails($id)
{
    // Find the user by ID
    $user = User::find($id);

    if ($user) {
        return response()->json([
            'success' => true,
            'user' => $user, // Return the user data as a JSON response
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ]);
    }
}



// =====================Update User Detail==================================
public function updateUser(Request $request)
{
    // Validate the incoming data
    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',  // Ensure the user ID exists in the database
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'user_name' => 'required|string|max:255',
        'zipcode' => 'required|string|max:10',
    ]);

    // Find the user by ID
    $user = User::find($validatedData['user_id']);

    if ($user) {
        // Update user data
        $user->first_name = $validatedData['first_name'];
        $user->last_name = $validatedData['last_name'];
        $user->email = $validatedData['email'];
        $user->user_name = $validatedData['user_name'];
        $user->zipcode = $validatedData['zipcode'];

        // Save the updated user
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'user' => $user,
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ]);
    }
}

// ========================End===================================

// =============================User Delete=======================

public function deleteUser(Request $request)
{
    // Validate the incoming request
    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',  // Ensure the user exists in the database
    ]);

    // Find the user by ID
    $user = User::find($validatedData['user_id']);

    if ($user) {
        // Delete the user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!',
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ]);
    }
}
// =================End=====================================
// ================================End User Edit Form======================================

    public function allManager(Request $request)
    {
        if($request->ajax()){
            $all_manager = "";
            $query = User::where('role', 'Manager')->leftJoin('branches', 'users.branch_id', 'branches.id');

            if($request->status){
                $query->where('users.status', $request->status);
            }

            $all_manager = $query->select('users.*', 'branches.branch_name')->get();

            return DataTables::of($all_manager)
            ->addIndexColumn()
            ->editColumn('branch_name', function($row){
                return'
                <span class="badge bg-dark">'.$row->branch_name.'</span>
                ';
            })
            ->editColumn('status', function($row){
                if($row->status == 'Active'){
                    $status = '
                    <span class="badge bg-success">'.$row->status.'</span>
                    <button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm statusBtn"><i class="bi bi-hand-thumbs-down-fill"></i></button>
                    ';
                }else{
                    $status = '
                    <span class="badge bg-warning">'.$row->status.'</span>
                    <button type="button" data-id="'.$row->id.'" class="btn btn-success btn-sm statusBtn"><i class="bi bi-hand-thumbs-up-fill"></i></button>
                    ';
                };
                return $status;
            })
            ->addColumn('action', function ($row) {
                $btn = '
                    <button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm editBtn" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-pencil-fill"></i></button>
                    <button type="button" data-id="'.$row->id.'" class="btn btn-success btn-sm viewBtn" data-bs-toggle="modal" data-bs-target="#viewModal"><i class="bi bi-eye"></i></button>
                ';
                return $btn;
            })
            ->rawColumns(['branch_name', 'status', 'action'])
            ->make(true);
        }

        $branchs = Branch::where('status', 'Active')->get();
        return view('admin.manager.index', compact('branchs'));
    }

    public function managerRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            User::create($request->except(['password', 'password_confirmation'])+[
                'role' => 'Manager',
                'password' => Hash::make($request->password),
            ]);
        }
    }

    public function managerEdit(string $id)
    {
        $manager = User::where('id', $id)->first();
        return response()->json($manager);
    }

    public function managerUpdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            '*' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $manager = User::findOrFail($id);
            $manager->update($request->all());
        }
    }

    public function managerStatus($id)
    {
        $user = User::findOrFail($id);
        if($user->status == "Active"){
            $user->status = "Inactive";
        }else{
            $user->status = "Active";
        }
        $user->save();
    }

    public function managerView($id)
    {
        $manager = User::where('id', $id)->first();
        return view('admin.manager.view', compact('manager'));
    }




    public function updateRole(Request $request)
    {
        // Get the user and update the 'can_edit' field
        $user = User::find($request->user_id);
    
        if ($user) {
            $user->role = $request->can_edit;
            $user->save();
    
            return response()->json(['success' => true]);
        }
    
        return response()->json(['success' => false, 'message' => 'User not found']);
    }
    

}
