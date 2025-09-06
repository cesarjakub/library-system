<?php
declare(strict_types=1);

namespace Model\Repositories;

use App\Model\Entities\Book;
use App\Model\Services\BookService;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class BookRepositoryTest extends TestCase
{
    private BookService $bookServiceMock;

    protected function setUp(): void
    {
        $this->bookServiceMock = Mockery::mock(BookService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testGetAllBooks(): void
    {
        $bookA = new Book('Title A', 'Author A', 2001, '9080736473292');
        $bookB = new Book('Title B', 'Author B', 2002, '9234743273010');

        $this->bookServiceMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn([$bookA, $bookB]);

        $books = $this->bookServiceMock->getAll();

        Assert::type('array', $books);
        Assert::true($books[0] instanceof Book);
        Assert::true($books[1] instanceof Book);
    }

    public function testCreateBook(): void
    {
        $book = new Book('New Book', 'Author Y', 2025, '9998887776665');

        $this->bookServiceMock
            ->shouldReceive('create')
            ->with('New Book', 'Author Y', 2025, '9998887776665')
            ->once()
            ->andReturn($book);

        $result = $this->bookServiceMock->create('New Book', 'Author Y', 2025, '9998887776665');

        Assert::same($book, $result);
    }
}

(new BookRepositoryTest())->run();