<?php

namespace App\Tests\Unit\VendingMachine\Domain\Service;

use App\VendingMachine\Domain\Model\ChangeBank;
use App\VendingMachine\Domain\Model\Coin;
use App\VendingMachine\Domain\Service\ChangeMaker;
use PHPUnit\Framework\TestCase;

final class ChangeMakerTest extends TestCase
{
    public function testReturnsEmptyForZeroAmount(): void
    {
        $maker = new ChangeMaker();
        $bank = new ChangeBank([5 => 10, 10 => 10, 25 => 10]);

        self::assertSame([], $maker->makeChange(0, $bank));
    }

    public function testMakesChangeWhenPossible(): void
    {
        $maker = new ChangeMaker();
        $bank = new ChangeBank([5 => 0, 10 => 10, 25 => 10]);

        self::assertSame(
            [Coin::C25, Coin::C10], $maker->makeChange(35, $bank));
    }

    public function testHandlesGreedyFailCaseWithLimitedCoins(): void
    {
        $maker = new ChangeMaker();
        $bank = new ChangeBank([5 => 0, 10 => 3, 25 => 1]);

        self::assertSame(
            [Coin::C10, Coin::C10, Coin::C10],
            $maker->makeChange(30, $bank));
    }

    public function testReturnsEmptyWhenCannotMakeExactChange(): void
    {
        $maker = new ChangeMaker();
        $bank = new ChangeBank([5 => 0, 10 => 0, 25 => 1]);

        self::assertSame([], $maker->makeChange(10, $bank));
    }

    public function testRejectsInvalidAmounts(): void
    {
        $maker = new ChangeMaker();
        $bank = new ChangeBank([5 => 10, 10 => 10, 25 => 10]);

        $this->expectException(\InvalidArgumentException::class);
        $maker->makeChange(-5, $bank);
    }
}
