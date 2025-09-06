<?php

namespace App\Command;


use App\Model\Services\BookService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'library:export')]
class ExportBooksCommand extends Command
{
    public function __construct(
        private BookService $bookService
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('format', InputArgument::REQUIRED, 'Export format: json|csv|xlsx')
            ->addArgument('output', InputArgument::REQUIRED, 'Output file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $format = strtolower($input->getArgument('format'));
        $outputFile = $input->getArgument('output');
        
        $books = $this->bookService->getAll();

        if (!$books) {
            $output->writeln('No books found in database.');
            return Command::SUCCESS;
        }

        return match ($format) {
            'json' => $this->exportJson($books, $outputFile, $output),
            'csv'  => $this->exportCsv($books, $outputFile, $output),
            default => $this->invalidFormat($format, $output),
        };
    }

    private function exportJson(array $books, mixed $outputFile, OutputInterface $output): int
    {
        $data = [];

        foreach ($books as $book) {
            $data[] = [
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'year' => $book->getYear(),
                'isbn' => $book->getIsbn(),
            ];
        }

        file_put_contents(
            $outputFile,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $output->writeln("Exported " . count($books) . " books to JSON: $outputFile");
        return Command::SUCCESS;
    }

    private function exportCsv(array $books, mixed $outputFile, OutputInterface $output): int
    {
        $handle = fopen($outputFile, 'w');
        fputcsv($handle, ['title', 'author', 'year', 'isbn'], ',', '"', '\\');

        foreach ($books as $book) {
            fputcsv($handle, [
                $book->getTitle(),
                $book->getAuthor(),
                $book->getYear(),
                $book->getIsbn(),
            ], ',', '"', '\\');
        }

        fclose($handle);
        $output->writeln("Exported " . count($books) . " books to CSV: $outputFile");
        return Command::SUCCESS;
    }

    private function invalidFormat(string $format, OutputInterface $output): int
    {
        $output->writeln("Invalid format: $format. Use json|csv|xlsx.");
        return Command::FAILURE;
    }
}