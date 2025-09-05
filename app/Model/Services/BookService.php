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

    public function getById(int $id): ?Book
    {
        return $this->bookRepository->findById($id);
    }

    public function create(string $title, string $author, int $year, string $isbn): Book
    {
        $book = new Book($title, $author, $year, $isbn);
        $this->bookRepository->save($book);
        return $book;
    }

    public function delete(Book $book): void
    {
        $this->bookRepository->delete($book);
    }

}