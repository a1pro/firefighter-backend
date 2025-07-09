<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Notifications\Frontend\Auth\UserNeedsPasswordReset;
use App\Repositories\Frontend\Access\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\MailSetting;
class ForgotPasswordController extends Controller
{
    /**
     * __construct.
     *
     * @param $repository
     */
    

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailOtp(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
    
        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first());
        }
    
        $data = $request->all();
    
        // Check if the email exists in the database
        $user = \DB::table('users')->where('email', $data['email'])->first();
        if (!$user) {
            return response()->json([
                'message' => 'Invalid email address. The email is not registered.',
                'success' => false,
                'data' => '',
            ]);
        }
    
        // Generate OTP
        $otp = mt_rand(1000, 9999);
    
        // OTP Email Body
        $body = '<p>Hi There,</p>
                 <p>The otp for email verification is: <b>' . $otp . '<b></p>
                 <p>With Regards ,</p>
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
        $sub = 'App Otp code related to Forgot Password Process';
    
        try {
            Mail::send('emails.common', ["data" => $body], function ($message) use ($from, $to, $sub) {
                $message->to($to);
                $message->subject($sub);
                $message->from($from, 'FireFighter');
            });
    
            // Save OTP to the database
            $updateEmailOtp = \DB::table('users')
                ->where('email', $data['email'])
                ->update(['optforgot' => $otp]);
    
                return response()->json([
                'message' => 'Email OTP sent successfully...',
                'success' => true,
                'data' => $otp,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email. Error: ' . $e->getMessage(),
                'success' => false,
                'data' => '',
            ]);
        }
    }

    /**
     * Verify the OTP for email.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmailOtp(Request $request)
    {
        
        $retmessage = "";
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            
        ]);

        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first());
        }

        $data = $request->all();

         $otpDetails = \DB::table('users')
            ->where('optforgot', $data['otp'])
            ->where('email', $data['email'])
            
            ->count();

        if ($otpDetails>0) {
            \DB::table('users')
            ->where('email', $data['email'])
            
            ->update(['optverifypassword' => '1','optforgot' => NULL]);


            $retmessage = "Otp Matched successfully...";

    
        

            return response()->json([
                'message' => $retmessage,
                'success' => true,
                'data' => '',
            ]);
        } else {
            $retmessage = "Otp does not match. Please try again.";
            return response()->json([
                'message' => $retmessage,
                'success' => false,
                'data' => '',
            ]);
        }
    }

    /**
     * Set a new password for the user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setNewPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            
        ]);

        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first());
        }

        $data = $request->all();
        $reqemail = $data['email'];

        $emailexist = \DB::table('users')
            ->where('email', $data['email'])
           
            ->count();

        if ($emailexist > 0) {
            $password = $request->get('password');
            $outpass = Hash::make($password);

            $client_data = \DB::table('users')
                ->where('email', $data['email'])
                
                ->update(['password' => $outpass]);

            if ($client_data) {
                return response()->json([
                    'message' => 'Password updated successfully...',
                    'success' => true,
                    'data' => '',
                ]);
            } else {
                return response()->json([
                    'message' => 'Something went wrong in update password...',
                    'success' => false,
                    'data' => '',
                ],200);
            }
        } else {
            return response()->json([
                'message' => "Email $reqemail does not exist.",
                'success' => false,
                'data' => '',
            ]);
        }
    }
}
