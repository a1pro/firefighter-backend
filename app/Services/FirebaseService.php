<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setAuthConfig(storage_path('firebase/firebase_credentials.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    private function getAccessToken()
    {
        $token = $this->client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }

    public function sendNotification(string $deviceToken, string $title, string $body, array $data = [])
    {
        $url = "https://fcm.googleapis.com/v1/projects/" . env('FIREBASE_PROJECT_ID') . "/messages:send";

        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->post($url, $payload);
        $responseBody = $response->json();
if ($response->failed()) {
        $errorMessage = $responseBody['error']['message'] ?? 'Unknown error';
        
        if (str_contains($errorMessage, 'registration token is not a valid')) {
            // 🚩 Handle invalid token (remove from DB, log, etc.)
            \Log::warning("Invalid FCM token detected: {$deviceToken}");
            // removeDeviceTokenFromDatabase($deviceToken);
        }

        \Log::error('FCM Notification failed', [
            'device_token' => $deviceToken,
            'error' => $errorMessage,
            'response' => $responseBody
        ]);
        }

    return $responseBody;
       
    }
}
