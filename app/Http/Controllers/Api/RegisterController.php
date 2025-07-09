<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\TwilioService;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
   // Validate the input
$validation = Validator::make($request->all(), [
    'user_name'     => 'required|unique:users,user_name', 
    'first_name'    => 'required',

    'email'         => 'required|email|unique:users,email',
    'phone_number'  => 'required|unique:users,phone_number',
    'password'      => 'required',
    'role'          => 'required',
]);

    
        // If validation fails, return an error response
        if ($validation->fails()) {
            return response()->json([
                'success'        => false,
                'status_code'    => 422,
                'message'        => $validation->messages()->first(),
                'data'           => [],
            ], 422);
        }
    
       $data=$request->all();
        // Proceed with storing the data
        $user = new User();
        $user->user_name        =        $request->input('user_name');
        $user->first_name       =        $request->input('first_name');
        $user->last_name        =        $request->input('last_name');
        $user->email            =        $request->input('email');
        $user->phone_number     =        $request->input('phone_number');
        $user->password         =        Hash::make($request->input('password'));
        $user->register_lat     =        $request->input('register_lat', null); 
        $user->register_long    =        $request->input('register_long', null); 
        $user->role             =        $request->input('role');   
        $user->zipcode          =        $request->input('zipcode',91505);
        $user->save();
    
        $this->registerOtp($data);
        // Return success response
        return response()->json([
            'success'        => true,
            'status_code'    => 200,
            'message'        => 'OTP has been sent successfully  to your registered email !!',
            'data'           => $user,
        ], 200);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
             /**
     * Log the user in.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerOtp($newdata)
    {
        
       $data = $newdata;
       $uname=$data['user_name'];
               
                           // Generate OTP
		$otp = mt_rand(100000, 999999);
	    
		// OTP Email Body
		$body = '<p>Hi <b>' .$uname. '</b>,</p>
		         <p>The otp for email verification is: <b>' . $otp . '<b></p>
		         <p>With Regards ,</p>
		         <p>Fire Fighter</p>';
    
        // Get 'from' email, or use fallback
        $mail_setting = MailSetting::first();
             $from = $mail_setting->from_address ?? 'noreply@example.com';  // Fallback email address if settings()->from_email is empty
    
		// Ensure $from email is valid
		if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
		    return $this->respond([
		        'message' => 'Invalid "from" email address.',
		        'success' => false,
		        'data' => '',
		    ]);
		}
	    
		// Ensure $to email is valid
		$to = $data['email'];
		if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
		    return $this->respond([
		        'message' => 'Invalid recipient email address.',
		        'success' => false,
		        'data' => '',
		    ]);
		}
    
        // Send the OTP email
               $sub = 'App Otp code related to Register Process';
    
   
            // Mail::send('emails.common', ["data" => $body], function ($message) use ($from, $to, $sub) {
            //     $message->to($to);
            //     $message->subject($sub);
            //     $message->from($from, 'Fire Fighter');
            // });
    
            // Save OTP to the database
            $updateEmailOtp = \DB::table('users')
                ->where('email', $data['email'])
                ->update(['otpregister_email' => $otp]);
    
            

        
    }
         /**
     * Log the user in.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginOtp(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ]);

        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first());
        }

        $credentials = $request->only(['email', 'password']);
       $data = $request->all();
        try {
            if (!Auth::attempt($credentials)) {
               
                              return $this->respond([
					    'success' => false, 
					    'message'   => 'These credentials do not match our records.',
					    'data'     => array("token"=>''),
					]);
            }

            $user = $request->user();
            if ($user->email_login_verify==0) {
               
                           // Generate OTP
		$otp = mt_rand(100000, 999999);
	    
		// OTP Email Body
		$body = '<p>Hi There,</p>
		         <p>The otp for email verification is: <b>' . $otp . '<b></p>
		         <p>With Regards ,</p>
		         <p>Bizzconnect</p>';
    
        // Get 'from' email, or use fallback
             $from = settings()->from_email ?? 'noreply@example.com';  // Fallback email address if settings()->from_email is empty
    
		// Ensure $from email is valid
		if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
		    return $this->respond([
		        'message' => 'Invalid "from" email address.',
		        'success' => false,
		        'data' => '',
		    ]);
		}
	    
		// Ensure $to email is valid
		$to = $data['email'];
		if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
		    return $this->respond([
		        'message' => 'Invalid recipient email address.',
		        'success' => false,
		        'data' => '',
		    ]);
		}
    
        // Send the OTP email
               $sub = 'App Otp code related to Register Process';
    
   
            Mail::send('emails.common', ["data" => $body], function ($message) use ($from, $to, $sub) {
                $message->to($to);
                $message->subject($sub);
                $message->from($from, 'Fire Fighter');
            });
    
            // Save OTP to the database
            $updateEmailOtp = \DB::table('users')
                ->where('email', $data['email'])
                ->update(['otplogin' => $otp]);
    
            return $this->respond([
                'message' => 'Email OTP sent successfully...',
                'success' => true,
                'data' => $otp,
                'screen' => 'otp',
            ]);
                    
            }
           elseif($user->email_login_verify==1){
		    $passportToken = $user->createToken('API Access Token');

		    // Save generated token
		    $passportToken->token->save();

		    $token = $passportToken->accessToken;
		     return $this->respond([
		    'success' => true, 
		    'message'   => trans('api.messages.login.success'),
		    'data'     => array("token"=>$token),
            'user_id'  => $user->id,
            'screen' => 'login'
		]);
        }
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }

        
    }
    
    
    /**
     * Verify the OTP for email.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtpLogin(Request $request)
    {
        
        $retmessage = "";
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            
        ]);

        // If validation fails, return an error response
        if ($validation->fails()) {
            return response()->json([
                'success'        => false,
                'status_code'    => 422,
                'message'        => $validation->messages()->first(),
                'data'           => [],
            ], 422);
        }
        $data = $request->all();

        $otpDetails = \DB::table('users')
            ->where('otpregister_email', $data['otp'])
            ->where('email', $data['email'])
            
            ->first();


        if (!empty($otpDetails)) {
            \DB::table('users')
                ->where('email', $data['email'])
                
                ->update(['email_reg_verify' => '1','otpregister_email' => NULL,'email_reg_time'=>date("Y-m-d H:i:s")]);

            $retmessage = "Otp Matched successfully...";
            
            $user = User::where('email', $data['email'])->first();
            
             // Generate a JWT token or session token for the authenticated user

              // Generate access token
        $accessToken = $user->createToken('auth_token')->plainTextToken;
             // Select only the needed attributes for the response
        $userData = $user->only(['user_name', 'first_name', 'email','phone_number', 'login_lat', 'login_long','role']);
        
        // Replace any null or empty values with empty string
        $userData = array_map(function ($value) {
            return $value === null ? '' : $value;
        }, $userData);
    
        // Include the profile photo URL if it exists
        if ($user->profile_photo_path) {
            $userData['profile_photo_url'] = Storage::url($user->profile_photo_path);
        }

            
              
		return response()->json([
                'success'        => true,
                'status_code'    => 200,
                'message'        => $retmessage,
                'data'           => $userData,
                "token" => $accessToken
          
            ], 200);
        } else {
            $retmessage = "Otp does not match. Please try again.";
            
            
            return response()->json([
                'success'        => false,
                'status_code'    => 422,
                'message'        => $retmessage,
                'data'           => array(),
            ], 422);
        }
    }
}
