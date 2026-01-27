<?php

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\ProcessActionsCommand;
use App\VendingMachine\Domain\Model\Coin;
use App\VendingMachine\Domain\Model\ProductType;
use App\VendingMachine\Domain\Model\VendingMachine;
use App\VendingMachine\Infrastructure\Parser\ServiceTokenParser;

final class ProcessActionsHandler
{
    public function __construct(
      private VendingMachine $vendingMachine,
      private ServiceTokenParser $serviceTokenParser
    ) {}

    /**
     * @return list<string>
     */
    public function handle(ProcessActionsCommand $command): array
    {
        $outputs = [];

        foreach ($command->tokens as $token) {
            $t = strtoupper(trim($token));
            if ($t == '') {
                continue;
            }

            if ($t === 'RETURN-COIN' || $t === 'RETURN COIN') {
                $coins = $this->vendingMachine->returnCoin();
                foreach ($coins as $coin) {
                    $outputs[] = $coin->toOutputToken();
                }
                continue;
            }

            if (str_starts_with($t, 'GET-')) {
                $product = ProductType::fromSelector($t);
                $outcome = $this->vendingMachine->vend($product);

                $outputs[] = $outcome->product->vendLabel();
                foreach ($outcome->change as $coin) {
                    $outputs[] = $coin->toOutputToken();
                }
                continue;
            }

            if (str_starts_with($t, 'SERVICE')) {
                $service = $this->serviceTokenParser->parse($token);
                $this->vendingMachine->service($service['inventory'], $service['changeBank']);
                continue;
            }

            $coin = Coin::fromActionToken($token);
            $this->vendingMachine->insertCoin($coin);
        }

        return $outputs;
    }
}