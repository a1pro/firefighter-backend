<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;


class UploadCsvController extends Controller
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
            'zipcode'       =>  'required',
             
        ]);

        $data = $request->all();
        $notifyuser                 =       new User();
        $notifyuser->first_name     =   $request->input('first_name');
        $notifyuser->last_name      =   $request->input('last_name');
        $notifyuser->email          =   $request->input('email');
        $notifyuser->phone_number   =   $request->input('phone_number');
        $notifyuser->zipcode        =   $request->input('zipcode');
        $notifyuser->password       =   Hash::make('123456');
        $notifyuser->role           =   'Notify';
        $notifyuser->save();
        return response()->json(['success' => true, 'notifyuser' => $notifyuser]);
    }


    public function edit($id)
    {
       
    }

    // Update method to update a category
    public function update(Request $request, $id)
{
    $request->validate([
        'first_name'   => 'required',
        'last_name'    => 'required',
        'email'        => 'required|email|unique:users,email,' . $id,
        'phone_number' => 'required|unique:users,phone_number,' . $id,
        'zipcode'      =>  'required,zipcode,' .$id, 
    ]);

    $notifyuser = User::findOrFail($id);
    $notifyuser->update([
        'first_name'   => $request->first_name,
        'last_name'    => $request->last_name,
        'email'        => $request->email,
        'phone_number' => $request->phone_number,
        'zipcode'      => $request->zipcode,
    ]);

    return response()->json(['success' => true, 'notifyuser' => $notifyuser]);
}

public function destroy($id)
{
    
}



// ----------------------------------------------------------------------------

//--------------------------------------------------------------------------
public function showUploadForm()
    {
    $agencies = getAgencies(); // Fetch all agencies
     
        return view('admin.csv_upload.csvupload', compact('agencies'));
    }

public function importUsers(Request $request)
{
    $data = $request->all();

    // Validate request inputs
    $request->validate([
        'agency' => 'required',
        'csv_file' => 'required|mimes:csv,txt|max:2048',
    ]);

    $agency = $data['agency'];
    $file = $request->file('csv_file');

    try {
        // Import and track skipped rows
        $import = new UsersImport($agency);
        Excel::import($import, $file);

        // Check for skipped rows
        $skippedRows = $import->getSkippedRows();

        if (count($skippedRows) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Import completed . But Skipped Duplicate rows: " . implode(', ', $skippedRows)
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Users imported successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'There was an error importing the users: ' . $e->getMessage()
        ]);
    }
}




}
