<?php

namespace Tests\Unit;

use App\Services\QuoteCalculatorService;
use PHPUnit\Framework\TestCase;

class QuoteCalculatorServiceTest extends TestCase
{
    private QuoteCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new QuoteCalculatorService();
    }

    public function test_charges_minimum_of_five_days_for_short_trips(): void
    {
        $result = $this->service->calculate([
            'destino' => 'NACIONAL',
            'data_inicio' => '2026-06-01',
            'data_fim' => '2026-06-03',
            'viajantes' => [
                [
                    'nome' => 'Maria',
                    'data_nascimento' => '1990-01-01',
                    'adicionais' => [],
                ],
            ],
        ]);

        $this->assertSame(5, $result['dias_cobrados']);
        $this->assertSame(50.0, $result['viajantes'][0]['subtotal']);
        $this->assertSame(50.0, $result['total_final']);
    }

    public function test_calculates_age_at_travel_start_date(): void
    {
        $result = $this->service->calculate([
            'destino' => 'EUROPA',
            'data_inicio' => '2026-07-10',
            'data_fim' => '2026-07-10',
            'viajantes' => [
                [
                    'nome' => 'Ana',
                    'data_nascimento' => '1990-03-15',
                    'adicionais' => [],
                ],
                [
                    'nome' => 'Bruno',
                    'data_nascimento' => '1990-07-15',
                    'adicionais' => [],
                ],
            ],
        ]);

        $this->assertSame(36, $result['viajantes'][0]['idade']);
        $this->assertSame(35, $result['viajantes'][1]['idade']);
    }

    public function test_denies_adventure_sports_with_warning_for_ineligible_traveler(): void
    {
        $result = $this->service->calculate([
            'destino' => 'EUROPA',
            'data_inicio' => '2026-07-10',
            'data_fim' => '2026-07-20',
            'viajantes' => [
                [
                    'nome' => 'João',
                    'data_nascimento' => '1948-11-02',
                    'adicionais' => ['ESPORTES_AVENTURA', 'BAGAGEM'],
                ],
            ],
        ]);

        $this->assertCount(1, $result['avisos']);
        $this->assertStringContainsString(
            'Esportes de aventura não aplicado para João',
            $result['avisos'][0]
        );
        $this->assertStringContainsString('18-64', $result['avisos'][0]);
        $this->assertSame(['BAGAGEM'], $result['viajantes'][0]['adicionais_aplicados']);
        $this->assertSame(77, $result['viajantes'][0]['idade']);
    }

    public function test_applies_group_discount_for_five_or_more_travelers(): void
    {
        $travelers = [];

        for ($index = 1; $index <= 5; $index++) {
            $travelers[] = [
                'nome' => "Viajante {$index}",
                'data_nascimento' => '1990-01-01',
                'adicionais' => [],
            ];
        }

        $result = $this->service->calculate([
            'destino' => 'NACIONAL',
            'data_inicio' => '2026-01-01',
            'data_fim' => '2026-01-05',
            'viajantes' => $travelers,
        ]);

        $this->assertSame(10, $result['desconto_grupo_percentual']);
        $this->assertSame(50.0, $result['viajantes'][0]['subtotal']);
        $this->assertSame(225.0, $result['total_final']);
    }

    public function test_calculates_complete_scenario_with_multiple_travelers_and_addons(): void
    {
        $result = $this->service->calculate([
            'destino' => 'EUROPA',
            'data_inicio' => '2026-07-10',
            'data_fim' => '2026-07-20',
            'viajantes' => [
                [
                    'nome' => 'Ana',
                    'data_nascimento' => '1990-03-15',
                    'adicionais' => ['BAGAGEM', 'ESPORTES_AVENTURA'],
                ],
                [
                    'nome' => 'João',
                    'data_nascimento' => '1948-11-02',
                    'adicionais' => ['ESPORTES_AVENTURA', 'BAGAGEM'],
                ],
            ],
        ]);

        $this->assertSame(11, $result['dias_cobrados']);
        $this->assertSame(0, $result['desconto_grupo_percentual']);

        $this->assertSame('Ana', $result['viajantes'][0]['nome']);
        $this->assertSame(36, $result['viajantes'][0]['idade']);
        $this->assertSame(335.5, $result['viajantes'][0]['subtotal']);
        $this->assertSame(
            ['ESPORTES_AVENTURA', 'BAGAGEM'],
            $result['viajantes'][0]['adicionais_aplicados']
        );

        $this->assertSame('João', $result['viajantes'][1]['nome']);
        $this->assertSame(77, $result['viajantes'][1]['idade']);
        $this->assertSame(517.0, $result['viajantes'][1]['subtotal']);
        $this->assertSame(['BAGAGEM'], $result['viajantes'][1]['adicionais_aplicados']);

        $this->assertCount(1, $result['avisos']);
        $this->assertSame(852.5, $result['total_final']);
    }
}
