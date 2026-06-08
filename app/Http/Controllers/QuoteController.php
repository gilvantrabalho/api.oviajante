<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Services\QuoteCalculatorService;

class QuoteController extends Controller
{
    public function store(QuoteRequest $request)
    {
        $quote = (new QuoteCalculatorService())->calculate($request->all());

        return response()->json($quote);
    }
}
