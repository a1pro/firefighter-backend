<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
class AuthController extends Controller
{
    /**
     * Log the user in.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
 

          
         //echo "test";
        /**
 * Handle user login
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\JsonResponse
 */

// public function login(Request $request)
// {
//     $input = $request->all();

//     $validation = Validator::make($input, [
//         'email'    => 'required|email',
//         'password' => 'required',
//     ]);

//     if ($validation->fails()) {
//         return response()->json([
//             'success' => false,
//             'message' => $validation->messages()->first(),
//             'data' => [],
//         ], 422);
//     }

//     $credentials = $request->only(['email', 'password']);

//     if (!Auth::attempt($credentials, $request->get('remember'))) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Invalid credentials',
//             'data' => [],
//         ], 401);
//     }

//     $user = Auth::user();
//     $accessToken = $user->createToken('auth_token')->plainTextToken;

//     return response()->json([
//         'success' => true,
//         'message' => 'Logged in successfully',
//         'data' => [
//             'token' => $accessToken,
            
//         ],
//     ], 200);
// }
  




    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revoke the current user's token
            $request->user()->currentAccessToken()->delete();
    
            // Respond with success message
            return response()->json([
                'status'  => true,
                'message' => "Profile has been logout successfully",
            ]);
        } catch (\Exception $e) {
            // Handle exceptions and return a clean error response
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred during logout. Please try again.',
                'error'   => $e->getMessage()
            ], 500); // Internal Server Error (HTTP 500)
        }
    }
    
    
    public function login(Request $request)
    {
        // Validate the input
        $validation = Validator::make($request->all(), [
            'user_name' => 'required_without:email', // user_name is required if email is not provided
            'email' => 'required_without:user_name|email', // email is required if user_name is not provided
            'password' => 'required',
            'login_lat' => 'nullable|numeric',
            'login_long' => 'nullable|numeric',
        ]);
    
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'status_code' => 422,
                'message' => $validation->messages()->first(),
                'data' => [],
            ], 422);
        }
    
        // Check if email or user_name is provided
        $loginField = $request->has('email') ? 'email' : 'user_name';
    
        // Use the provided login field and password for authentication
        $credentials = [
            $loginField => $request->input($loginField),
            'password' => $request->input('password'),
        ];
    
        // Attempt to authenticate the user
        if (!Auth::attempt($credentials, $request->get('remember'))) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Invalid credentials',
                'data' => [],
            ], 401);
        }
    
        // Get the authenticated user
        $user = Auth::user();
    
        // If login latitude and longitude are provided, save them to the user's record
        if ($request->has('login_lat') && $request->has('login_long')) {
            $user->login_lat = $request->input('login_lat');
            $user->login_long = $request->input('login_long');
            $user->save();
        }
    
        // Hide unnecessary fields
        $user->makeHidden(['created_at', 'updated_at']);
    
        // Generate access token
        $accessToken = $user->createToken('auth_token')->plainTextToken;
    
        // Select only the needed attributes for the response
        $userData = $user->only(['user_name', 'first_name', 'email', 'phone_number', 'zipcode','login_lat', 'login_long', 'role']);
    
        // Replace any null or empty values with an empty string
        $userData = array_map(function ($value) {
            return $value === null ? '' : $value;
        }, $userData);
    
        // Include the profile photo URL if it exists
        if ($user->profile_photo_path) {
            $userData['profile_photo_url'] = Storage::url($user->profile_photo_path);
        }
    
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'token' => $accessToken,
            'message' => 'Logged in successfully',
            'data' => $userData,
        ], 200);
    }
    

    


}
