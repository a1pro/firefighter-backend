<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;use Validator;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\EditDragIcons;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class DragIconOtpController extends Controller
{
    public function dragIconOtp(Request $request)
    {
        // Get authenticated user data
        $data = Auth::user();
        // Uncomment if needed to debug user data
        // print_r($data);
        // die;
        
        // Get the user id
        $userId = Auth::id();
    
        // Validate input data
        $validation = Validator::make($request->all(), [
            'building_id' => 'required', // Building ID is required
        ]);
    
        // If validation fails, return the error
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->messages()->first(),
                'data'    => [],
            ]);
        }
    
        // Check if the user is authenticated
        if (!$userId) {
            return response()->json([
                'success'    => false,
                'status_code'=> 401,
                'message'    => "User not authenticated",
            ]);
        }
    
        // Get the building ID and validate the email
        $buildingId = $request->building_id;
        $uname = $data['user_name'];
    
        // Generate OTP
        $otp = mt_rand(100000, 999999);
        
        // OTP Email Body
        $body = '<p>Hi <b>' . $uname . '</b>,</p>
                 <p>The OTP for Building Modify Access is: <b>' . $otp . '</b></p>
                 <p>With Regards,</p>
                 <p>Fire Fighter</p>';
    
        // Get 'from' email, or use fallback
        $mail_setting = MailSetting::first();
        $from = $mail_setting->from_address ?? 'noreply@example.com';  // Fallback email address if settings()->from_email is empty
    
        // Ensure $from email is valid
        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'message' => 'Invalid "from" email address.',
                'success' => false,
                'data' => '',
            ]);
        }
    
        // Ensure $to email is valid
        $to = $data['email'];
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'message' => 'Invalid recipient email address.',
                'success' => false,
                'data' => '',
            ]);
        }
    
        // Send the OTP email
        $sub = 'App OTP code related to Drag Icon Edit Access';
    
        // Mail::send('emails.common', ["data" => $body], function ($message) use ($from, $to, $sub) {
        //     $message->to($to);
        //     $message->subject($sub);
        //     $message->from($from, 'Fire Fighter');
        // });
    
        // Save OTP to the database along with the required fields
        $otpData = [
            'building_id'         => $buildingId,
            'otp'                 => $otp,
            'otp_verify'          => 0, // Initially OTP is not verified
            'otp_date'            => now(),
            'otp_verify_date'     => null, // OTP verify date will be null initially
            'edited_by'           => $userId, // Store the user id who edited it
        ];
    
        // Check if the email already exists in the table, and update if necessary
       
            // Insert a new OTP record
            \DB::table('edit_drag_icons')
                ->insert(array_merge($otpData, ['edited_by' => $userId]));
        
    
        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'data'    => $otpData,
        ]);
    }
    


    public function verifyOtpDragIcons(Request $request)
    {
        $userId = Auth::id();
        $retmessage = "";
        $validation = Validator::make($request->all(), [
            'building_id'  => 'required',
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

        $otpDetails = \DB::table('edit_drag_icons')
            ->where('otp', $data['otp'])
            ->where('building_id',$data['building_id'])
            ->where('edited_by', $userId)
            
            ->first();


        if (!empty($otpDetails)) {
            \DB::table('edit_drag_icons')
                ->where('id', $otpDetails->id)
                
                ->update(['otp_verify' => '1','otp'=>NULL,'otp_verify_date'=>date("Y-m-d H:i:s")]);

            $retmessage = "Otp Matched successfully...";
            
       
            
              
		return response()->json([
                'success'        => true,
                'status_code'    => 200,
                'message'        => $retmessage,
                'data'           => [
                    'buidling_id' => $data['building_id'],
                ],
                // "token" => $accessToken
          
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
