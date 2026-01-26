<?php

namespace App\VendingMachine\Domain\Model;

final class Inventory
{
    /** @var array<string,int> */
    private array $counts;

    /**
     * @param array<ProductType,int> $counts
     */

    public function __construct(array $counts)
    {
        $normalized = [];
        foreach (ProductType::cases() as $type) {
            $normalized[$type->value] = (int)($counts[$type->value] ?? 0);
            if ($normalized[$type->value] < 0) {
                throw new \InvalidArgumentException('Inventotory count cannot be negative');
            }
        }
        $this->counts = $normalized;
    }

    public static function withDefaults(): self
    {
        return new self([
            ProductType::WATER => 10,
            ProductType::JUICE => 10,
            ProductType::SODA => 10
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