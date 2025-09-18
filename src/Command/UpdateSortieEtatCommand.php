<?php

namespace App\Command;

use App\Service\SortieService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-sortie-etat',
    description: 'Add a short description for your command',
)]
class UpdateSortieEtatCommand extends Command
{
    public function __construct(private SortieService $sortieService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sortieUpdated = $this->sortieService->updateEtatSortie();
        $output->writeln(sprintf('sortieUpdated'. $sortieUpdated));

        return Command::SUCCESS;
    }
}
