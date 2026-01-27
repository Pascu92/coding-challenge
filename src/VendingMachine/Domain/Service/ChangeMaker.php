<?php

namespace App\VendingMachine\Domain\Service;

use App\VendingMachine\Domain\Model\ChangeBank;
use App\VendingMachine\Domain\Model\Coin;

final class ChangeMaker
{
    /**
     * @return list<Coin>
     */
    public function makeChange(int $amountCents, ChangeBank $changeBank): array
    {
        if ($amountCents === 0) {
            return [];
        }

        if ($amountCents < 0 || $amountCents % 5 !== 0) {
            throw new \InvalidArgumentException('Change must be non-negative and multiple of 5.');
        }

        $max25 = min(intdiv($amountCents, 25), $changeBank->available(25));
        for ($n25 = $max25; $n25 >= 0; $n25--) {
            $remainingAfter25 = $amountCents - (25 * $n25);

            $max10 = min(intdiv($remainingAfter25, 10), $changeBank->available(10));
            for ($n10 = $max10; $n10 >= 0; $n10--) {
                $remaining = $remainingAfter25 - (10 * $n10);
                $n5 = intdiv($remaining, 5);

                if ($n5 * 5 !== $remaining) {
                    continue;
                }
                if ($n5 <= $changeBank->available(5)) {
                    $result = [];
                    for ($i = 0; $i < $n25; $i++) { $result[] = Coin::C25; }
                    for ($i = 0; $i < $n10; $i++) { $result[] = Coin::C10; }
                    for ($i = 0; $i < $n5;  $i++) { $result[] = Coin::C5; }
                    return $result;
                }
            }
        }

        return [];
    }
}