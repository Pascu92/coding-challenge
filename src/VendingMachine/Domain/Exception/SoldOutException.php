<?php

namespace App\VendingMachine\Domain\Exception;

final class SoldOutException extends DomainException
{
    public function __construct(string $product)
    {
        parent::__construct(sprintf('SOLD_OUT: %s', $product));
    }
}