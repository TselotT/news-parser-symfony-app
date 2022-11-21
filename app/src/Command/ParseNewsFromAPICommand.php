<?php

namespace App\Command;

use App\Repository\NewsRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'parseNewsFromApi',
    description: 'Parse the news and update our database frequently!!',
)]
class ParseNewsFromAPICommand extends Command
{
    public function __construct(
        NewsRepository $newsRepository
    ) 
    {
        parent::__construct();
        $this->newsRepository = $newsRepository;
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
        
        $this->newsRepository->parseNewsFromAPI();
        return Command::SUCCESS;
    }
}
