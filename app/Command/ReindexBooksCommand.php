<?php

namespace App\Command;

use App\Model\Services\BookIndexer;
use App\Model\Services\BookService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'library:reindex-books')]
class ReindexBooksCommand extends Command
{
    public function __construct(
        private BookService $bookService,
        private BookIndexer $bookIndexer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->bookService->getAll() as $book) {
            $this->bookIndexer->index($book);
        }

        $output->writeln('All books reindexed into Elasticsearch.');
        return self::SUCCESS;
    }
}