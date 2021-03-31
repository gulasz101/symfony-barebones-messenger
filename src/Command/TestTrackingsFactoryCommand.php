<?php

namespace App\Command;

use App\Entity\Tracking;
use App\Support\EntityManagerInterfaceAware;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

class TestTrackingsFactoryCommand extends Command
{
    use EntityManagerInterfaceAware;

    protected static $defaultName = 'test:trackings:factory';
    protected static $defaultDescription = '';

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('count', InputArgument::OPTIONAL, 'Amount of trackings to factory (<100)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('count');

        if ($arg1) {
            for ($i = 0; $i<$arg1; $i++) {
                $this->em->persist((new Tracking())->setTrackingNumber(Uuid::v4()));
            }

            $this->em->flush();
        }

        return Command::SUCCESS;
    }
}
