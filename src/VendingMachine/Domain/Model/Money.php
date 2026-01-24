<?php

namespace App\VendingMachine\Domain\Model;

final class Money
{
    public function __construct(private int $cents)
    {
        if ($cents < 0) {
           throw new \InvalidArgumentException('Money cannot be negative');
        }
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function isGreaterThanOrEqual(self $other): bool
    {
        return $this->cents >= $other->cents;
    }
}