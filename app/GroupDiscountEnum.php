<?php

namespace App;

enum GroupDiscountEnum
{
    case GROUP_1; // Nº viagantes entre 1 e 4
    case GROUP_2; // Nº viagantes entre 5 ou mais

    public function getDiscount(): float
    {
        return match ($this) {
            self::GROUP_1 => 0, // 0% de desconto
            self::GROUP_2 => 0.1, // 10% de desconto
        };
    }

    public static function fromTravelerCount(int $count): self
    {
        return $count >= 5 ? self::GROUP_2 : self::GROUP_1;
    }

    public function getDiscountPercentage(): int
    {
        return (int) round($this->getDiscount() * 100);
    }
}
