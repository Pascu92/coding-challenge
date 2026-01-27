<?php

namespace App\VendingMachine\Domain\Model;

final class Inventory
{
    /** @var array<string,int> */
    private array $counts;

    /**
     * @param array<mixed,int> $counts
     */
    public function __construct(array $counts)
    {
        $normalized = [];

        foreach (ProductType::cases() as $type) {
            $key = $type->value;
            $value = $counts[$key] ?? 0;
            $normalized[$key] = (int) $value;

            if ($normalized[$key] < 0) {
                throw new \InvalidArgumentException('Inventory count cannot be negative');
            }
        }
        $this->counts = $normalized;
    }

    public static function withDefaults(): self
    {
        return new self([
            'WATER' => 10,
            'JUICE' => 10,
            'SODA'  => 10,
        ]);
    }

    public function has(ProductType $type): bool
    {
        return ($this->counts[$type->value] ?? 0) > 0;
    }

    public function decrement(ProductType $type): void
    {
        if (!$this->has($type)) {
            throw new \LogicException('Cannot decrement empty stock');
        }
        $this->counts[$type->value]--;
    }
}