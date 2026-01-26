<?php

namespace App\VendingMachine\Domain\Model;

enum ProductType: string
{
    case WATER = 'WATER';
    case JUICE = 'JUICE';
    case SODA = 'SODA';

    public static function fromSelector(string $selector): self
    {
        $s = strtoupper(trim($selector));

        return match ($s) {
            'GET-WATER' => self::WATER,
            'GET-JUICE' => self::JUICE,
            'GET-SODA' => self::SODA,
            default => throw new \InvalidArgumentException(sprintf('Invalid selector: %s', $s)),
        };
    }

    public function priceCents(): int
    {
        return match ($this) {
            self::WATER => 65,
            self::JUICE => 100,
            self::SODA => 150,
        };
    }

    public function vendLabel(): string
    {
        return $this->value;
    }
}