<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.gemini.api_key');


        if (!$this->apiKey) {
            Log::error('Gemini API key is not set.');
            throw new \Exception('Gemini API key is not set.');
        }
    }

    public function ask($prompt)
    {
        try {
            $response = $this->client->post('https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent?key=' . $this->apiKey, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $body = json_decode($response->getBody(), true);
                return $body['candidates'][0]['content']['parts'][0]['text'] ?? 'No content in response';
            } else {
                return 'Unexpected error: ' . $response->getStatusCode();
            }
        } catch (RequestException $e) {
            Log::error('Gemini API request failed', ['error' => $e->getMessage()]);
            return 'Sorry, there was an error processing your request: ' . $e->getMessage();
        }
    }
}
