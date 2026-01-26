<?php

namespace App\VendingMachine\Domain\Model;

final readonly class VendOutcome
{
    /**
     * @param list<Coin> $change
     */
    public function __construct(
        public ProductType $product,
        public array $change
    ) {}
}