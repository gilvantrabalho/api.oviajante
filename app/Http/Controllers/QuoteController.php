<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Repositories\Contracts\QuoteRepositoryInterface;
use App\Services\QuoteCalculatorService;

class QuoteController extends Controller
{
    public function __construct(
        private readonly QuoteCalculatorService $calculator,
        private readonly QuoteRepositoryInterface $quoteRepository,
    ) {}

    public function store(QuoteRequest $request)
    {
        $payload = $request->validated();
        $quote = $this->calculator->calculate($payload);

        $this->quoteRepository->create($payload, $quote);

        return response()->json($quote);
    }

    public function index()
    {
        $quotes = $this->quoteRepository->all()->map(fn ($quote) => [
            'id' => $quote->id,
            'destino' => $quote->destino,
            'data_inicio' => $quote->data_inicio->format('Y-m-d'),
            'data_fim' => $quote->data_fim->format('Y-m-d'),
            'total_final' => $quote->total_final,
            'viajantes_count' => count($quote->request_payload['viajantes'] ?? []),
            'consultado_em' => $quote->created_at?->toIso8601String(),
            'resultado' => $quote->response_payload,
        ]);

        return response()->json(['data' => $quotes]);
    }
}
