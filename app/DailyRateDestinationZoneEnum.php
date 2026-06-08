<?php

namespace App;

enum DailyRateDestinationZoneEnum
{
    case NACIONAL;
    case AMERICAS;
    case EUROPA;

    public function getDailyRate(): int
    {
        return match ($this) {
            self::NACIONAL => 10, // R$ 10,00
            self::AMERICAS => 16, // R$ 16,00
            self::EUROPA => 22, // R$ 22,00
        };
    }

    public static function fromDestination(string $destination): self
    {
        return match ($destination) {
            'NACIONAL' => self::NACIONAL,
            'AMERICAS' => self::AMERICAS,
            'EUROPA' => self::EUROPA,
        };
    }
}
