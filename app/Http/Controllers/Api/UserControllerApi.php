<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;

class UserControllerApi extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getuser_detail(Request $request)
    {
        // Check if the user is authenticated
        if (!$request->user()) {
            // If not authenticated, return a 401 Unauthorized response
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'User not authenticated',
            ], 401);
        }


        $user = $request->user();

        // Hide unnecessary fields like 'created_at' and 'updated_at'
        $user->makeHidden(['created_at', 'updated_at']);

        // Add the profile photo URL to the user data
        if ($user->profile_photo) {
            $user->profile_photo_url = url('storage/uploads/profile_pic/' . $user->profile_photo);
        } else {
            $user->profile_photo_url = ''; // Return an empty string if no profile photo exists
        }

        // Convert the user object to an array to process the data
        $user = $user->toArray();

        // Iterate over the user data and replace null values with empty strings
        array_walk_recursive($user, function (&$value) {
            if (is_null($value)) {
                $value = ''; // Replace null with an empty string
            }
        });

        // Return the user data in the response
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'User data fetched successfully',
            'data' => $user,
        ], 200);
    }



    public function getUserProfile()
    {
        $userId = Auth::id();

        DB::beginTransaction();
        if(!Auth::check()){
            return response()->json([
                'success'       =>  false,
                'status_code'   =>  401,
                'message'      =>   'User not authenticated'

            ]);
        }

        try{

            $userData = User::select('first_name','last_name','user_name','email','role','zipcode')->where('id',$userId)->get();
          
                return response()->json([
                    'success'       =>  true,
                    'status_code'   =>   200,
                    'message'       =>   'User Data fetch successfully',
                    'data'          =>    $userData,
                ]);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json([
                'success'       =>  false,
                'status_code'   =>  500,
                'message'       =>  'Something went wrong: ' .$e->getMessage(),
            ]);   
        }
    }


    public function generateUserUpdate(Request $request)
    {



        $user = $request->user();
    }

    public function updateUserProfile(Request $request)
    {
        $userid = Auth::id();
       


        // Check if the user is authenticated
        if (!$request->user()) {
            // If not authenticated, return a 401 Unauthorized response
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'User not authenticated',
            ], 401);
        }

        // Validate incoming request
        $validation = Validator::make($request->all(), [
            'zipcode' => 'nullable',
            'username' => 'nullable',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->first(),
                'data'    => [],
            ]);
        }

    

        // Get the authenticated user
        $user = User::find($userid);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if ($request->has('user_name')) {
            $user->user_name = $request->user_name;
        }

        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }

        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }

        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }

        if ($request->has('zipcode')) {
            $user->zipcode = $request->zipcode;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Your profile has been updated successfully.',
            'data' => [$user],
        ]);
    }


    // =================Get User Profile=========================================

}
