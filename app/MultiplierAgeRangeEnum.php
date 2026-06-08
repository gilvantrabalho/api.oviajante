<?php

namespace App;

enum MultiplierAgeRangeEnum
{
    case YOUNG; // 0 a 17
    case ADULT; // 18 a 64
    case SENIOR; // 65 anos ou mais

    public function getMultiplier(): float
    {
        return match ($this) {
            self::YOUNG => 0.5, // 0.5x
            self::ADULT => 1.0, // 1x
            self::SENIOR => 2.0, // 2x
        };
    }

    public static function fromAge(int $age): self
    {
        return match (true) {
            $age <= 17 => self::YOUNG,
            $age <= 64 => self::ADULT,
            default => self::SENIOR,
        };
    }

    public function allowsAdventureSports(): bool
    {
        return $this === self::ADULT;
    }
}
