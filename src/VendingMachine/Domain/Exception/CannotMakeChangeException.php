<?php

namespace App\VendingMachine\Domain\Exception;

final class CannotMakeChangeException extends DomainException
{
    public function __construct(int $changeCents)
    {
        parent::__construct(sprintf('CANNOT_MAKE_CHANGE: %d', $changeCents));
    }
}