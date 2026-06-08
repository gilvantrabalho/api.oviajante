<?php

namespace App\Repositories;

use App\Models\Quote;
use App\Repositories\Contracts\QuoteRepositoryInterface;
use Illuminate\Support\Collection;

class QuoteRepository implements QuoteRepositoryInterface
{
    public function __construct(
        private readonly Quote $model
    ) {}

    public function create(array $request, array $response): Quote
    {
        return $this->model->newQuery()->create([
            'destino' => $request['destino'],
            'data_inicio' => $request['data_inicio'],
            'data_fim' => $request['data_fim'],
            'request_payload' => $request,
            'response_payload' => $response,
            'total_final' => $response['total_final'],
        ]);
    }

    public function all(): Collection
    {
        return $this->model
            ->newQuery()
            ->latest()
            ->get();
    }
}
