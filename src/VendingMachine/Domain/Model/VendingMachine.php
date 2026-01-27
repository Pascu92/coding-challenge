<?php

namespace App\VendingMachine\Domain\Model;

use App\VendingMachine\Domain\Service\ChangeMaker;
use App\VendingMachine\Domain\Exception\CannotMakeChangeException;
use App\VendingMachine\Domain\Exception\InsufficientFundsException;
use App\VendingMachine\Domain\Exception\SoldOutException;

final class VendingMachine
{
    private Inventory $inventory;
    private ChangeBank $changeBank;
    private InsertedCoins $insertedCoins;

    public function __construct(
        Inventory $inventory,
        ChangeBank $changeBank,
        private ChangeMaker $changeMaker,
    ) {
        $this->inventory = $inventory;
        $this->changeBank = $changeBank;
        $this->insertedCoins = new InsertedCoins();
    }

    public static function withDefaults(ChangeMaker $changeMaker): self
    {
        return new self(Inventory::withDefaults(), ChangeBank::withDefaults(), $changeMaker);
    }

    public function insertCoin(Coin $coin): void
    {
        $this->insertedCoins->add($coin);
    }

    /** @return list<Coin> */
    public function returnCoin(): array
    {
        $coins = $this->insertedCoins->all();
        $this->insertedCoins->clear();
        return $coins;
    }

    public function service(Inventory $inventory, ChangeBank $changeBank): void
    {
        $this->inventory = $inventory;
        $this->changeBank = $changeBank;
    }

    public function vend(ProductType $product): VendOutcome
    {
        if (!$this->inventory->has($product)) {
            throw new SoldOutException($product->vendLabel());
        }

        $inserted = $this->insertedCoins->totalCents();
        $price = $product->priceCents();

        if ($inserted < $price) {
            throw new InsufficientFundsException($price, $inserted);
        }

        $changeCents = $inserted - $price;
        $tempBank = new ChangeBank($this->changeBank->snapshot());
        $tempBank->addMany($this->insertedCoins->all());

        $changeCoins = $this->changeMaker->makeChange($changeCents, $tempBank);
        if ($changeCents > 0 && $changeCoins === []) {
            throw new CannotMakeChangeException($changeCents);
        }

        $this->inventory->decrement($product);
        $this->changeBank->addMany($this->insertedCoins->all());
        $this->changeBank->removeMany($changeCoins);
        $this->insertedCoins->clear();

        return new VendOutcome($product, $changeCoins);
    }
}