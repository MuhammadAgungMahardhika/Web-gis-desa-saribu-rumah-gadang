<?php

namespace App\Controllers\Web;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourcePresenter;

class Gemma extends ResourcePresenter
{

    protected $helpers = ['auth', 'url', 'filesystem', 'url', 'form'];


    public function index()
    {
        return view('maps/gemma');
    }

    public function ask()
    {
        $inquiry = $this->request->getVar('inquiry');

        $response = $this->sendInquiryToGroqAPI($inquiry);


        return $this->respond(['answer' => $response], ResponseInterface::HTTP_OK);
    }

    private function sendInquiryToGroqAPI($inquiry)
    {
        $url = "https://api.groq.com/openai/v1/chat/completions";
        $headers = [
            'Authorization' => 'Bearer ' . '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', // Ensure the API key is in your .env
            'Content-Type' => 'application/json',
        ];

        $data = [
            'messages' => [
                ['role' => 'user', 'content' => $inquiry],
            ],
            'model' => 'llama-3.1-8b-instant', // or any model you want to use
            'max_tokens' => 400,
            'temperature' => 0.7,
        ];

        $client = \Config\Services::curlrequest();
        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'json' => $data,
        ]);

        $body = $response->getBody();
        $result = json_decode($body, true);

        return $result['choices'][0]['message']['content'] ?? 'No response from the AI';
    }
}
