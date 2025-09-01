<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;

class GeminiController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function show()
    {
        return view('gemini');
    }

    public function ask(Request $request)
    {
        $prompt = $request->input('prompt');
        $response = $this->gemini->ask($prompt);
        return redirect()->route('gemini.show')->with('response', $response);
    }
}
