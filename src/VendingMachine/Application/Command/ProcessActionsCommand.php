<?php

namespace App\VendingMachine\Application\Command;

final readonly class ProcessActionsCommand
{
    /**
     * @param list<string> $tokens
     */
    public function __construct(public array $tokens) {}
}