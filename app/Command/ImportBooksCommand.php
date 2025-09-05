<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Entities\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'library:import')]
class ImportBooksCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file with books');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');

        if (!is_readable($file)) {
            $output->writeln(sprintf('The file %s could not be read.', $file));
            return Command::FAILURE;
        }
        $count = 0;
        foreach (file($file) as $line) {
            [$title, $author, $year, $isbn] = str_getcsv($line);

            if (!$title || !$author || !$year || !$isbn) {
                $output->writeln(sprintf('Skipped invalid line: %s', $line));
                continue;
            }
            $book = new Book($title, $author, (int)$year, $isbn);
            $this->em->persist($book);
            $count++;
        }
        $this->em->flush();
        $output->writeln(sprintf('Import completed: %s books were added.', $count));
        return Command::SUCCESS;
    }
}
