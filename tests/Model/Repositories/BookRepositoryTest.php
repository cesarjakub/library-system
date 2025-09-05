<?php
declare(strict_types=1);

namespace Model\Repositories;

use App\Model\Entities\Book;
use App\Model\Repositories\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class BookRepositoryTest extends TestCase
{
    private BookRepository $repository;

    protected function setUp(): void
    {
        $this->repository = Mockery::mock(BookRepository::class)->makePartial();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testFindAllReturnsArrayOfBooks(): void
    {
        $bookA = new Book('Title A', 'Author A', 2001, '9080736473292');
        $bookB = new Book('Title B', 'Author B', 2002, '9234743273010');

        $this->repository
            ->shouldReceive('findAll')
            ->andReturn([$bookA, $bookB]);

        $books = $this->repository->findAll();

        Assert::type('array', $books);
        Assert::true($books[0] instanceof Book);
        Assert::true($books[1] instanceof Book);
    }
}

(new BookRepositoryTest())->run();