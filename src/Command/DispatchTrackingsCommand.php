<?php

namespace App\Command;

use App\Message\BatchableAsyncAwaitProcessTracking;
use App\Message\BatchableFiberProcessTracking;
use App\Message\EmptyMessage;
use App\Message\ProcessTracking;
use App\Support\Illuminate\HttpAware;
use App\Support\Services\TrackingResolverAware;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(
    name: 'dispatch:trackings',
    description: '',
)]
class DispatchTrackingsCommand extends Command
{
    #[Required]
    public MessageBusInterface $messageBus;

    protected function configure()
    {
        $this
            ->addArgument('count', InputArgument::OPTIONAL, '', 100)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        for ($i = 0; $i < $input->getArgument('count'); $i++) {
            $stackMessage = new ProcessTracking();
            $batchableFiberMessage = new BatchableFiberProcessTracking();
            $batchableAsynAwaitMessage = new BatchableAsyncAwaitProcessTracking();

            $this->messageBus->dispatch($stackMessage);
            $this->messageBus->dispatch($batchableFiberMessage);
            $this->messageBus->dispatch($batchableAsynAwaitMessage);

            $output->writeln(sprintf("#%s %s", $i, $stackMessage->getTrackingNumber()));
            $output->writeln(sprintf("#%s %s", $i, $batchableFiberMessage->getTrackingNumber()));
            $output->writeln(sprintf("#%s %s", $i, $batchableAsynAwaitMessage->getTrackingNumber()));
            $output->writeln('');
        }

        return Command::SUCCESS;
    }
}
