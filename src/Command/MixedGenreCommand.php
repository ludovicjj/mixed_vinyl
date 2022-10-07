<?php

namespace App\Command;

use App\Service\MixRepository;
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
    private array $mixes;

    public function __construct(
        private MixRepository $mixRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('genre', InputArgument::REQUIRED, 'Mix genre')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->mixes = $this->mixRepository->findAll();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->enterGenre($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $genre = $input->getArgument('genre');

        $mixesFilterByGenre = array_filter($this->mixes, function($mix) use ($genre) {
            return $mix['genre'] === $genre;
        });
        $mixesFilterByGenreCount = count($mixesFilterByGenre);
        $io->success("Selected mix for given genre: \"{$genre}\", total result {$mixesFilterByGenreCount}");

        $io->table(
            ['Title', 'TrackCount'],
            array_map(function($mix) {
                return [$mix['title'], $mix['trackCount']];
            }, $mixesFilterByGenre)
        );

        return Command::SUCCESS;
    }

    private function enterGenre(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $genres = array_unique(array_map(function($mix) {
            return $mix['genre'];
        }, $this->mixes));

        $genreQuestion = new ChoiceQuestion(
            "Select Mixes Genre",
            $genres,
            'Rock'
        );
        $genre = $helper->ask($input, $output, $genreQuestion);
        $input->setArgument('genre', $genre);
    }
}
