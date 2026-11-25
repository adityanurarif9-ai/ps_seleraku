<?php

namespace App\Http\Controllers;

use App\Services\SeloAssistant;
use Illuminate\Http\Request;

class SeloController extends Controller
{
    public function __invoke(Request $request, SeloAssistant $assistant)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:500']]);

        return response()->json($assistant->answer($data['message']))->header('Cache-Control', 'no-store, private');
    }
}
