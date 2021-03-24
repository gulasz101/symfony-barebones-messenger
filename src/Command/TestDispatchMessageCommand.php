<?php

namespace App\Command;

use App\Message\EmptyMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class TestDispatchMessageCommand extends Command
{
    protected static $defaultName = 'test:dispatch-message';
    protected static $defaultDescription = '';

    /** @required */
    public MessageBusInterface $messageBus;

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        for ($i = 0; $i < 20; $i++) {
            $this->messageBus->dispatch(new EmptyMessage(self::class . ' #' . $i));
        }

        return Command::SUCCESS;
    }
}
