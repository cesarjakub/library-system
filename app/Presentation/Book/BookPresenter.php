<?php
declare(strict_types=1);

namespace App\Presentation\Book;

use App\Model\Services\BookService;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;

class BookPresenter extends Presenter
{
    public function __construct(
        private BookService $bookService
    ){}

    protected function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->error('You are not authorized to access this page.', 403);
        }
    }

    public function renderDefault(): void
    {
        $this->template->books = $this->bookService->getAll();
    }

    public function renderDetail(int $id): void
    {
       $book = $this->bookService->getById($id);

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

        $this->bookService->create($values->title, $values->author, $values->year, $values->isbn);

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

        $book = $this->bookService->getById((int) $values->id);
        if (!$book) {
            $this->error('Book was not found.');
        }

        $this->bookService->delete($book);

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