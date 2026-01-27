<?php

namespace App\VendingMachine\Infrastructure\Symfony\Command;

use App\VendingMachine\Application\Command\ProcessActionsCommand as AppCommand;
use App\VendingMachine\Application\Handler\ProcessActionsHandler;
use App\VendingMachine\Domain\Exception\DomainException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:vending',
    description: 'Run vending machine actions, e.g. "1,0.25,GET-WATER"'
)]
final class VendingMachineCommand extends Command
{
    public function __construct(private ProcessActionsHandler $handler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('actions', InputArgument::REQUIRED, 'Comma-separated actions');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $raw = (string) $input->getArgument('actions');
        $tokens = array_map('trim', explode(',', $raw));

        try {
            $result = $this->handler->handle(new AppCommand($tokens));
        } catch (DomainException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
            return Command::FAILURE;
        } catch (\Throwable $e) {
            $output->writeln('<error>UNEXPECTED_ERROR: '.$e->getMessage().'</error>');
            return Command::FAILURE;
        }

        $output->writeln(implode(',', $result));
        return Command::SUCCESS;
    }
}