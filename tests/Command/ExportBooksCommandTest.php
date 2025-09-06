<?php

namespace Command;

use App\Command\ExportBooksCommand;
use App\Model\Entities\Book;
use App\Model\Services\BookService;
use Mockery;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

final class ExportBooksCommandTest extends TestCase
{

    private array $books;
    private $bookService;
    private ExportBooksCommand $command;

    protected function setUp(): void
    {
        $this->books = [
            new Book('Title1', 'Author1', 2020, '1234567890'),
            new Book('Title2', 'Author2', 2021, '0987654321'),
        ];

        $this->bookService = Mockery::mock(BookService::class);
        $this->bookService->shouldReceive('getAll')
            ->andReturn($this->books);

        $this->command = new ExportBooksCommand($this->bookService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testExportJson(): void
    {
        $commandTester = new CommandTester($this->command);

        $file = __DIR__ . '/books_test.json';
        if (file_exists($file)) {
            unlink($file);
        }

        $commandTester->execute([
            'format' => 'json',
            'output' => $file,
        ]);

        Assert::true(file_exists($file), 'JSON file should exist');

        $data = json_decode(file_get_contents($file), true);
        Assert::count(2, $data);
        Assert::contains('Title1', $data[0]['title']);

        unlink($file);
    }

    public function testExportCsv(): void
    {
        $commandTester = new CommandTester($this->command);

        $file = __DIR__ . '/books_test.csv';
        if (file_exists($file)) {
            unlink($file);
        }

        $commandTester->execute([
            'format' => 'csv',
            'output' => $file,
        ]);

        Assert::true(file_exists($file), 'CSV file should exist');

        $lines = file($file, FILE_IGNORE_NEW_LINES);
        Assert::count(3, $lines);
        Assert::contains('Title1', $lines[1]);

        unlink($file);
    }

    public function testInvalidFormat(): void
    {
        $commandTester = new CommandTester($this->command);

        $exitCode = $commandTester->execute([
            'format' => 'xml',
            'output' => 'dummy',
        ]);

        $exitCode = $commandTester->getStatusCode();
        Assert::same(1, $exitCode);
        Assert::contains('Invalid format', $commandTester->getDisplay());
    }
}
(new ExportBooksCommandTest())->run();