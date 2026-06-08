<?php

namespace App\Services;

use App\DailyRateDestinationZoneEnum;
use App\GroupDiscountEnum;
use App\MultiplierAgeRangeEnum;
use Carbon\Carbon;

class QuoteCalculatorService
{
    private const BAGGAGE_DAILY_RATE = 3.00;
    private const ADVENTURE_SPORTS_SURCHARGE = 0.25;

    public function calculate(array $data): array
    {
        $daysCharged = $this->calculateDaysCharged(
            $data['data_inicio'],
            $data['data_fim']
        );

        $destination = DailyRateDestinationZoneEnum::fromDestination($data['destino']);
        $dailyRate = $destination->getDailyRate();

        $travelers = [];
        $warnings = [];
        $groupTotal = 0;

        foreach ($data['viajantes'] as $traveler) {
            $age = $this->calculateAge(
                $traveler['data_nascimento'],
                $data['data_inicio']
            );

            $ageRange = MultiplierAgeRangeEnum::fromAge($age);
            $base = $dailyRate * $daysCharged;
            $subtotal = $base * $ageRange->getMultiplier();
            $appliedAddons = [];

            if (in_array('ESPORTES_AVENTURA', $traveler['adicionais'] ?? [])) {
                if ($ageRange->allowsAdventureSports()) {
                    $subtotal += $subtotal * self::ADVENTURE_SPORTS_SURCHARGE;
                    $appliedAddons[] = 'ESPORTES_AVENTURA';
                } else {
                    $warnings[] =
                        "Esportes de aventura não aplicado para {$traveler['nome']}: fora da faixa etária permitida (18-64).";
                }
            }

            if (in_array('BAGAGEM', $traveler['adicionais'] ?? [])) {
                $subtotal += self::BAGGAGE_DAILY_RATE * $daysCharged;
                $appliedAddons[] = 'BAGAGEM';
            }

            $groupTotal += $subtotal;

            $travelers[] = [
                'nome' => $traveler['nome'],
                'idade' => $age,
                'subtotal' => round($subtotal, 2),
                'adicionais_aplicados' => $appliedAddons,
            ];
        }

        $groupDiscount = GroupDiscountEnum::fromTravelerCount(count($data['viajantes']));
        $discountRate = $groupDiscount->getDiscount();
        $totalFinal = $groupTotal * (1 - $discountRate);

        return [
            'dias_cobrados' => $daysCharged,
            'viajantes' => $travelers,
            'avisos' => $warnings,
            'desconto_grupo_percentual' => $groupDiscount->getDiscountPercentage(),
            'total_final' => round($totalFinal, 2, PHP_ROUND_HALF_UP),
        ];
    }

    private function calculateDaysCharged(
        string $startDate,
        string $endDate
    ): int {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $days = $start->diffInDays($end) + 1;

        return max($days, 5);
    }

    private function calculateAge(
        string $birthDate,
        string $travelStartDate
    ): int {
        $birth = Carbon::parse($birthDate);
        $travelStart = Carbon::parse($travelStartDate);

        return $birth->diffInYears($travelStart);
    }
}
