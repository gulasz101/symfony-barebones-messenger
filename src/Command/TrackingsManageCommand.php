<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Tracking;
use App\Message\ProcessTracking;
use App\Repository\TrackingRepository;
use App\Support\EntityManagerInterfaceAware;
use App\Support\MessageBusInterfaceAware;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrackingsManageCommand extends Command
{
    use EntityManagerInterfaceAware;
    use MessageBusInterfaceAware;

    /** @required */
    public TrackingRepository $trackingRepository;

    protected static $defaultName = 'app:trackings:manage';
    protected static $defaultDescription = '';

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->em->persist(new Tracking());
        $this->em->flush();

        $trackings = $this->trackingRepository->createQueryBuilder('t')
            ->where('t.status IS NULL')
            ->getQuery()
            ->execute();

        foreach ($trackings as $tracking) {
            $this->bus->dispatch(new ProcessTracking($tracking));
        }

        $io->info('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
