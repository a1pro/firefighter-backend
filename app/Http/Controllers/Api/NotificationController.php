<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendTestNotification()
    {
        $deviceToken = 'recipient-device-token';
        $title = 'New FCM V1 Notification';
        $body = 'Hello from FCM V1!';
        $data = ['key' => 'value'];

        $response = $this->firebaseService->sendNotification($deviceToken, $title, $body, $data);

        return response()->json($response);
    }
}


