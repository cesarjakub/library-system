<?php
declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\Book;
use Doctrine\ORM\EntityRepository;


class BookRepository extends EntityRepository
{

    public function findById(int $id): ?Book
    {
        return $this->find($id);
    }

    public function addBook(Book $book): void
    {
        $this->_em->persist($book);
        $this->_em->flush();
    }

    public function removeBook(Book $book): void
    {
        $this->_em->remove($book);
        $this->_em->flush();
    }
}