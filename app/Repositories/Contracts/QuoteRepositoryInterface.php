<?php

namespace App\Repositories\Contracts;

use App\Models\Quote;
use Illuminate\Support\Collection;

interface QuoteRepositoryInterface
{
    public function create(array $request, array $response): Quote;

    public function all(): Collection;
}
