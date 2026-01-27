<?php

namespace App\VendingMachine\Infrastructure;

use App\VendingMachine\Domain\Model\VendingMachine;
use App\VendingMachine\Domain\Service\ChangeMaker;

final class VendingMachineFactory
{
    public function __construct(private ChangeMaker $changeMaker) {}

    public function create(): VendingMachine
    {
        return VendingMachine::withDefaults($this->changeMaker);
    }
}