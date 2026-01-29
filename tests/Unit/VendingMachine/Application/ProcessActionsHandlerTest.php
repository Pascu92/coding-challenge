<?php

namespace App\Tests\Unit\VendingMachine\Application;

use App\VendingMachine\Application\Command\ProcessActionsCommand;
use App\VendingMachine\Application\Handler\ProcessActionsHandler;
use App\VendingMachine\Domain\Exception\CannotMakeChangeException;
use App\VendingMachine\Domain\Exception\SoldOutException;
use App\VendingMachine\Domain\Model\VendingMachine;
use App\VendingMachine\Domain\Service\ChangeMaker;
use App\VendingMachine\Infrastructure\Parser\ServiceTokenParser;
use PHPUnit\Framework\TestCase;

final class ProcessActionsHandlerTest extends TestCase
{
    private function handler(): ProcessActionsHandler
    {
        return new ProcessActionsHandler(
            VendingMachine::withDefaults(new ChangeMaker()),
            new ServiceTokenParser()
        );
    }

    public function testExample1BuySodaExactChange(): void
    {
        $result = $this->handler()->handle(new ProcessActionsCommand(['1', '0.25', '0.25', 'GET-SODA']));
        self::assertSame(['SODA'], $result);
    }

    public function testExample2ReturnCoin(): void
    {
        $result = $this->handler()->handle(new ProcessActionsCommand(['0.10', '0.10', 'RETURN-COIN']));
        self::assertSame(['0.10', '0.10'], $result);
    }

    public function testExample3BuyWaterWithChange(): void
    {
        $result = $this->handler()->handle(new ProcessActionsCommand(['1', 'GET-WATER']));
        self::assertSame(['WATER', '0.25', '0.10'], $result);
    }

    public function testServiceCanSetSoldOut(): void
    {
        $this->expectException(SoldOutException::class);

        $this->handler()->handle(new ProcessActionsCommand([
            'SERVICE items=WATER:0;JUICE:10;SODA:10 change=5:20;10:20;25:20',
            '1',
            'GET-WATER'
        ]));
    }

    public function testServiceCanRemoveChangeAndCauseCannotMakeChange(): void
    {
        $this->expectException(CannotMakeChangeException::class);

        $this->handler()->handle(new ProcessActionsCommand([
            'SERVICE items=WATER:10;JUICE:10;SODA:10 change=5:0;10:0;25:0',
            '1',
            'GET-WATER'
        ]));
    }
}
