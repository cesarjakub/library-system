<?php
declare(strict_types=1);

namespace App\Model\Services;

use App\Model\Entities\Book;
use App\Model\Repositories\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    private BookRepository $bookRepository;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var BookRepository $repo */
        $repo = $em->getRepository(Book::class);
        $this->bookRepository = $repo;
    }

    public function getAll(): array
    {
        return $this->bookRepository->findAllBooks();
    }

    public function getPage(int $offset, int $limit): array
    {
        return $this->bookRepository->findBy([], null, $limit, $offset);
    }

    public function getById(int $id): ?Book
    {
        return $this->bookRepository->findById($id);
    }

    public function getByIds(array $ids): array
    {
        if (!$ids) return [];
        return $this->bookRepository->findBy(['id' => $ids]);
    }

    public function create(string $title, string $author, int $year, string $isbn): Book
    {
        $book = new Book($title, $author, $year, $isbn);
        $this->bookRepository->save($book);
        return $book;
    }

    public function update(Book $book, array $data): void
    {
        if (isset($data['title'])) {
            $book->setTitle($data['title']);
        }
        if (isset($data['author'])) {
            $book->setAuthor($data['author']);
        }
        if (isset($data['year'])) {
            $book->setYear((int)$data['year']);
        }
        if (isset($data['isbn'])) {
            $book->setIsbn($data['isbn']);
        }
        if (isset($data['coverPath'])) {
            $book->setCoverPath($data['coverPath']);
        }

        $this->bookRepository->save($book);
    }

    public function getCount(): int
    {
        return $this->bookRepository->count([]);
    }

    public function delete(Book $book): void
    {
        $this->bookRepository->delete($book);
    }

    public function getYearRange(): array
    {
        $books = $this->getAll();
        if (!$books) {
            $currentYear = (int) date('Y');
            return [$currentYear, $currentYear];
        }

        $years = array_map(fn($book) => $book->getYear(), $books);

        $min = min($years);
        $max = max($years);

        return [$min, $max];
    }

}