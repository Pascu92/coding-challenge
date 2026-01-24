<?php

namespace App\VendingMachine\Domain\Model;

enum Coin: int
{
    case C5 = 5;
    case C10 = 10;
    case C25 = 25;
    case C100 = 100;

    public static function fromActionToken(string $token): self
    {
        $t = trim($token);

        return match($t) {
            '0.05' => self::C5,
            '0.10' => self::C10,
            '0.25' => self::C25,
            '1', '1.0', '1.00' => self::C100,
            default => throw new \InvalidArgumentException(sprintf('Invalid coin token: %s', $t))
        };
    }

    public function isChangeCoin(): bool
    {
        return $this !== self::C100;
    }

    public function toOutputToken(): string
    {
        return match($this) {
            self::C5 => '0.05',
            self::C10 => '0.10',
            self::C25 => '0.25',
            self::C100 => '1',
        };
    }
}