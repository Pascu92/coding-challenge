<?php

namespace App\Tests\Unit\VendingMachine\Domain\Model;

use App\VendingMachine\Domain\Exception\CannotMakeChangeException;
use App\VendingMachine\Domain\Exception\InsufficientFundsException;
use App\VendingMachine\Domain\Exception\SoldOutException;
use App\VendingMachine\Domain\Model\ChangeBank;
use App\VendingMachine\Domain\Model\Coin;
use App\VendingMachine\Domain\Model\Inventory;
use App\VendingMachine\Domain\Model\ProductType;
use App\VendingMachine\Domain\Model\VendingMachine;
use App\VendingMachine\Domain\Service\ChangeMaker;
use PHPUnit\Framework\TestCase;

final class VendingMachineTest extends TestCase
{
    public function testBuySodaWithExactChange(): void
    {
        $machine = VendingMachine::withDefaults(new ChangeMaker());

        $machine->insertCoin(Coin::C100);
        $machine->insertCoin(Coin::C25);
        $machine->insertCoin(Coin::C25);

        $outcome = $machine->vend(ProductType::SODA);

        self::assertSame(ProductType::SODA, $outcome->product);
        self::assertSame([], $outcome->change);
    }

    public function testReturnCoinReturnsInsertedCoinsInInsertionOrder(): void
    {
        $machine = VendingMachine::withDefaults(new ChangeMaker());
        $machine->insertCoin(Coin::C10);
        $machine->insertCoin(Coin::C25);
        $machine->insertCoin(Coin::C5);
        $returned = $machine->returnCoin();

        self::assertSame([Coin::C10, Coin::C25, Coin::C5], $returned);
    }

    public function testInsufficientFundsThrows(): void
    {
        $machine = VendingMachine::withDefaults(new ChangeMaker());
        $machine->insertCoin(Coin::C25);

        $this->expectException(InsufficientFundsException::class);
        $machine->vend(ProductType::SODA);
    }

    public function testSoldOutThrows(): void
    {
        $inventory = new Inventory(['WATER' => 0, 'JUICE' => 10, 'SODA' => 10]);
        $bank = new ChangeBank([5 => 20, 10 => 20, 25 => 20]);
        $machine = new VendingMachine($inventory, $bank, new ChangeMaker());
        $machine->insertCoin(Coin::C100);

        $this->expectException(SoldOutException::class);
        $machine->vend(ProductType::WATER);
    }

    public function testBuyWaterWithChangeFromBankAndInsertedCoins(): void
    {
        $inventory = new Inventory(['WATER' => 10, 'JUICE' => 10, 'SODA' => 10]);
        $bank = new ChangeBank([5 => 0, 10 => 20, 25 => 20]);

        $machine = new VendingMachine($inventory, $bank, new ChangeMaker());
        $machine->insertCoin(Coin::C100);
        $outcome = $machine->vend(ProductType::WATER);

        self::assertSame(ProductType::WATER, $outcome->product);
        self::assertSame([Coin::C25, Coin::C10], $outcome->change);
    }

    public function testCannotMakeChangeThrowsAndKeepsInsertedMoney(): void
    {
        $inventory = new Inventory(['WATER' => 10, 'JUICE' => 10, 'SODA' => 10]);
        $bankEmpty = new ChangeBank([5 => 0, 10 => 0, 25 => 0]);
        $machine = new VendingMachine($inventory, $bankEmpty, new ChangeMaker());
        $machine->insertCoin(Coin::C100);

        try {
            $machine->vend(ProductType::WATER);
            self::fail('Expected CannotMakeChangeException was not thrown');
        } catch (CannotMakeChangeException) {
        }

        $returned = $machine->returnCoin();
        self::assertSame([Coin::C100], $returned);
    }
}
