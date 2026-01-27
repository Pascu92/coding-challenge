<?php

namespace App\VendingMachine\Infrastructure\Parser;

use App\VendingMachine\Domain\Model\ChangeBank;
use App\VendingMachine\Domain\Model\Inventory;
use App\VendingMachine\Domain\Model\ProductType;

final class ServiceTokenParser
{
    /**
     * Formato esperado:
     * SERVICE items=WATER:10,JUICE:10,SODA:10 change=5:20,10:15,25:10
     *
     * @return array{inventory: Inventory, changeBank: ChangeBank}
     */
    public function parse(string $token): array
    {
        $raw = trim($token);
        $raw = trim(preg_replace('/^SERVICE\s*/i', '', $raw) ?? '');

        $parts = $this->parseKeyValueParts($raw);

        $inventory = $this->parseInventory($parts['items'] ?? '');
        $changeBank = $this->parseChangeBank($parts['change'] ?? '');

        return ['inventory' => $inventory, 'changeBank' => $changeBank];
    }

    /**
     * @return array<string,string>
     */
    private function parseKeyValueParts(string $raw): array
    {
        $out = [];
        $chunks = preg_split('/\s+/', trim($raw)) ?: [];

        foreach ($chunks as $chunk) {
            if (!str_contains($chunk, '=')) {
                continue;
            }
            [$k, $v] = explode('=', $chunk, 2);
            $out[strtolower(trim($k))] = trim($v);
        }

        return $out;
    }

    private function parseInventory(string $raw): Inventory
    {
        // WATER:10,JUICE:10,SODA:10
        $pairs = array_filter(array_map('trim', explode(';', $raw)));
        $counts = [];

        foreach ($pairs as $pair) {
            [$name, $count] = array_map('trim', explode(':', $pair, 2));
            $type = ProductType::from(strtoupper($name)); // enum cases: WATER/JUICE/SODA
            $counts[$type->value] = (int) $count;
        }

        return new Inventory($counts);
    }

    private function parseChangeBank(string $raw): ChangeBank
    {
        // 5:20,10:15,25:10
        $pairs = array_filter(array_map('trim', explode(';', $raw)));
        $coins = [];

        foreach ($pairs as $pair) {
            [$cents, $count] = array_map('trim', explode(':', $pair, 2));
            $c = (int) $cents;

            if (!in_array($c, [5, 10, 25], true)) {
                throw new \InvalidArgumentException('ChangeBank only supports 5,10,25 cents coins');
            }

            $coins[$c] = (int) $count;
        }

        return new ChangeBank($coins);
    }
}