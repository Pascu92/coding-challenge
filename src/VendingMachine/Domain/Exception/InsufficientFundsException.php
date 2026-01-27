<?php

namespace App\VendingMachine\Domain\Exception;

final class InsufficientFundsException extends DomainException
{
    public function __construct(int $requiredCents, int $insertedCents)
    {
        parent::__construct(sprintf('INSUFFICIENT_FOUNDS: required=$d inserted=$d', $requiredCents, $insertedCents));
    }
}