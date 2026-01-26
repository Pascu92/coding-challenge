<?php

namespace App\VendingMachine\Domain\Model;

final class ChangeBank
{
    /** @var array<int,int> cents => count */
    private array $coins;

    /**
     * @param array<int,int> $coins e.g. [5=>10, 10=>10, 25=>10]
     */
    public function __construct(array $coins)
    {
        $this->coins = [
            5  => max(0, (int)($coins[5]  ?? 0)),
            10 => max(0, (int)($coins[10] ?? 0)),
            25 => max(0, (int)($coins[25] ?? 0)),
        ];
    }

    public static function withDefaults(): self
    {
        return new self([5 => 20, 10 => 20, 25 => 20]);
    }

    public function available(int $cents): int
    {
        return $this->coins[$cents] ?? 0;
    }

    /** @param list<Coin> $coins */
    public function addMany(array $coins): void
    {
        foreach ($coins as $coin) {
            if ($coin->isChangeCoin()) {
                $this->coins[$coin->value] = ($this->coins[$coin->value] ?? 0) + 1;
            }
        }
    }

    /** @param list<Coin> $coins */
    public function removeMany(array $coins): void
    {
        foreach ($coins as $coin) {
            if (!$coin->isChangeCoin()) {
                continue;
            }
            $c = $coin->value;
            if (($this->coins[$c] ?? 0) <= 0) {
                throw new \LogicException('Attempted to remove coin not available');
            }
            $this->coins[$c]--;
        }
    }

    /** @return array<int,int> */
    public function snapshot(): array
    {
        return $this->coins;
    }
}
