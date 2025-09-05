<?php
declare(strict_types=1);

namespace App\Presentation\Book;

use Doctrine\ORM\EntityRepository;
use Nette\Application\UI\Presenter;
use App\Model\Entities\Book;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;

class BookPresenter extends Presenter
{
    private EntityRepository $bookRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->bookRepo = $em->getRepository(Book::class);
    }

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->error('You are not authorized to access this page.', 403);
        }
    }

    public function renderDefault(): void
    {
        $this->template->books = $this->bookRepo->findAll();
    }

    public function renderDetail(int $id): void
    {
        $book = $this->bookRepo->getBook($id);

        if (!$book) {
            $this->error('Book was not found.');
        }

        $this->template->book = $book;
    }

    protected function createComponentBookForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Title:')
            ->setRequired('Please enter the book title.');
        $form->addText('author', 'Author:')
            ->setRequired('Please enter the author.');
        $form->addInteger('year', 'Year:')
            ->setRequired('Please enter the publication year.');
        $form->addText('isbn', 'ISBN:')
            ->setRequired('Please enter ISBN.');

        $form->addSubmit('send', 'Save');
        $form->onSuccess[] = $this->bookFormSucceeded(...);

        return $form;
    }

    private function bookFormSucceeded(Form $form, \stdClass $values): void
    {
        if (!$this->getUser()->isInRole('admin')) {
            $this->error('You are not authorized to perform this action.', 403);
        }

        $book = new Book($values->title, $values->author, $values->year, $values->isbn);

        $this->bookRepo->saveBook($book);

        $this->flashMessage('Book has been added.', 'success');
        $this->redirect('Book:default');
    }

    protected function createComponentDeleteForm(): Form
    {
        $form = new Form;
        $form->addProtection();
        $form->addHidden('id', $this->getParameter('id'));
        $form->addSubmit('send', 'Delete');
        $form->onSuccess[] = $this->deleteFormSucceeded(...);
        return $form;
    }

    public function deleteFormSucceeded(Form $form, \stdClass $values): void
    {
        if (!$this->getUser()->isInRole('admin')) {
            $this->error('You are not authorized to perform this action.', 403);
        }

        $book = $this->bookRepo->find($values->id);
        if (!$book) {
            $this->error('Book was not found.');
        }

        $this->bookRepo->deleteBook($book);

        $this->flashMessage('Book has been deleted.', 'success');
        $this->redirect('Book:default');
    }

    public function actionAdd(): void
    {
        if (!$this->getUser()->isInRole('admin')) {
            $this->error('You are not authorized to access this page.', 403);
        }
    }

}