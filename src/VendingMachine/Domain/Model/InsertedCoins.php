<?php

namespace App\VendingMachine\Domain\Model;

final class InsertedCoins
{
    /** @var list<Coin> */
    private array $coins = [];

    public function add(Coin $coin): void
    {
        $this->coins[] = $coin;
    }

    /** @return list<Coin> */
    public function all(): array
    {
        return $this->coins;
    }

    public function totalCents(): int
    {
        $sum = 0;
        foreach ($this->coins as $coin) {
            $sum += $coin->value;
        }

        return $sum;
    }

    public function clear(): void
    {
        $this->coins = [];
    }
}