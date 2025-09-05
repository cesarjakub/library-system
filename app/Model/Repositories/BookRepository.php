<?php
declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\Book;
use Doctrine\ORM\EntityRepository;


class BookRepository extends EntityRepository
{

    public function saveBook(Book $book): void
    {
        $this->_em->persist($book);
        $this->_em->flush();
    }

    public function deleteBook(Book $book): void
    {
        $this->_em->remove($book);
        $this->_em->flush();
    }

    public function findById(int $id): Book
    {
        return $this->find($id);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}