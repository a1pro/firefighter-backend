<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class FaqController extends Controller
{
    
    public function getFaq()
{
    $user = Auth::user();
    $userId = Auth::id();

    // Check if the user is authenticated
    if (!$userId) {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => 'User not authenticated',
        ], 401);
    }

    // Fetch FAQs with the category name, ensuring categories are sorted by their creation date (latest first)
    $faqs = DB::table('faq')
        ->join('faqcategory', 'faq.category_id', '=', 'faqcategory.id')
        ->select('faq.*', 'faqcategory.category_name as category_name', 'faqcategory.created_at')
        ->orderBy('faqcategory.created_at', 'asc') // Sort categories by creation date (latest first)
        ->get();

    // If no FAQs are found
    if ($faqs->isEmpty()) {
        return response()->json([
            'success' => false,
            'status_code' => 404,
            'message' => 'No FAQs found',
        ], 404);
    }

    // Group FAQs by category_name
    $groupedFaqs = $faqs->groupBy('category_name');

    // Prepare the data for the response
    $responseData = $groupedFaqs->map(function ($faqsByCategory, $categoryName) {
        $faqItems = $faqsByCategory->map(function ($faq) {
            return [
                'faq_id' => $faq->id,
                'question' => strip_tags($faq->question),  // Remove HTML tags from question
                'answer' => strip_tags($faq->answer),  // Remove HTML tags from answer
            ];
        });

        return [
            'category_name' => $categoryName,
            'faqs' => $faqItems,
        ];
    });

    return response()->json([
        'success' => true,
        'status_code' => 200,
        'message' => 'FAQs List by Category',
        'data' => $responseData,
    ], 200);
}

    

public function searchFaq(Request $request)
{
    $user = Auth::user();
    $userId = Auth::id();

    if (!$userId) {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => 'User not authenticated',
        ], 401);
    }

    $request->validate([
        'search' => 'required|string|min:1',
    ]);

    $searchTerm = $request->input('search');

    // First, search in local DB
    $faqs = DB::table('faq')
        ->join('faqcategory', 'faq.category_id', '=', 'faqcategory.id')
        ->select('faq.*', 'faqcategory.category_name as category_name')
        ->where('faq.question', 'like', '%' . $searchTerm . '%')
        ->orWhere('faq.answer', 'like', '%' . $searchTerm . '%')
        ->get();

    if (!$faqs->isEmpty()) {    
        $groupedFaqs = $faqs->groupBy('category_name');

        $responseData = $groupedFaqs->map(function ($faqsByCategory, $categoryName) {
            $faqItems = $faqsByCategory->map(function ($faq) {
                return [
                    'faq_id' => $faq->id,
                    'question' => strip_tags($faq->question),
                    'answer' => strip_tags($faq->answer),
                ];
            });

            return [
                'category_name' => $categoryName,
                'faqs' => $faqItems,
            ];
        });

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'FAQs retrieved from database',
            'data' => $responseData,
        ], 200);
    }

    // If no local FAQ found, use OpenAI
    $openAiResponse = $this->searchUsingChatGPT($searchTerm);

    return response()->json([
        'success' => true,
        'status_code' => 200,
        'message' => 'Response from OpenAI',
        'data' => [
            'category_name' => 'AI Assistant',
            'faqs' => [
                [
                    'faq_id' => null,
                    'question' => $searchTerm,
                    'answer' => $openAiResponse,
                ]
            ]
        ]
    ]);
}

private function searchUsingChatGPT($question)
{
    $apiKey = env('OPENAI_API_KEY');

    if (empty($apiKey) || !str_starts_with($apiKey, 'sk-')) {
        \Log::error('OpenAI API Key is missing or invalid.');
        return "Sorry, I'm unable to find an answer right now.";
    }

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',  // You can use gpt-4 if needed
            'messages' => [
                ['role' => 'user', 'content' => $question]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        \Log::debug('OpenAI API Response', $response->json());

        if (!$response->successful()) {
            \Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return "Sorry, I'm unable to find an answer right now.";
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? "No answer found.";

    } catch (\Exception $e) {
        \Log::error('OpenAI API Exception: ' . $e->getMessage());
        return "Sorry, I'm unable to find an answer right now.";
    }
}






}
