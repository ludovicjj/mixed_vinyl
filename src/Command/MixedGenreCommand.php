<?php

namespace App\Command;

use App\Repository\VinylMixRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mixed-genre',
    description: 'A command that can do... only one thing.',
)]
class MixedGenreCommand extends Command
{
    public function __construct(
        private VinylMixRepository $mixRepository
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('genre', InputArgument::REQUIRED, 'Mix genre')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->enterGenre($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $genre = $input->getArgument('genre');
        $mixes = $this->mixRepository->findBy(['genre' => $genre]);
        $count = count($mixes);

        $io->success("Selected mix for given genre: \"{$genre}\", total result {$count}");

        $io->table(
            ['Title', 'TrackCount'],
            array_map(function($mix) {
                return [$mix->getTitle(), $mix->getTrackCount()];
            }, $mixes)
        );

        return Command::SUCCESS;
    }

    private function enterGenre(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $mixes = $this->mixRepository->findAll();

        $genres = array_unique(array_map(fn($mix) => $mix->getGenre(), $mixes));

        $genreQuestion = new ChoiceQuestion(
            "Select Mixes Genre",
            $genres
        );
        $genre = $helper->ask($input, $output, $genreQuestion);
        $input->setArgument('genre', $genre);
    }
}
