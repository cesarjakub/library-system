<?php
declare(strict_types=1);

namespace App\Presentation\Book;

use App\Model\Services\BookSearchService;
use App\Model\Services\BookService;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use Nette\Utils\Image;
use Nette\Utils\Paginator;

class BookPresenter extends Presenter
{
    public function __construct(
        private BookService $bookService,
        private BookSearchService $bookIndexer
    ){}

    protected function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->error('You are not authorized to access this page.', 403);
        }
    }

    public function renderDefault(?string $q = null, int $page = 1): void
    {
        $itemsPerPage = 10;
        $this->template->query = $q;

        $paginator = new Paginator;
        $paginator->setItemsPerPage($itemsPerPage);
        $paginator->setPage($page);

        if ($q) {
            $totalItems = $this->bookIndexer->countSearchResults($q);
            $paginator->setItemCount($totalItems);

            $from = $paginator->getOffset();
            $ids = $this->bookIndexer->search($q, $from, $itemsPerPage);

            $books = $this->bookService->getByIds($ids);
        } else {
            $totalItems = $this->bookService->getCount();
            $paginator->setItemCount($totalItems);

            $books = $this->bookService->getPage($paginator->getOffset(), $itemsPerPage);
        }

        $this->template->books = $books;
        $this->template->paginator = $paginator;
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
        $this->requireAdmin();

        $book = $this->bookService->create($values->title, $values->author, $values->year, $values->isbn);
        $this->bookIndexer->index($book);
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
        $this->requireAdmin();

        $book = $this->bookService->getById((int) $values->id);
        if (!$book) {
            $this->error('Book was not found.');
        }

        $this->bookService->delete($book);
        $this->bookIndexer->delete($book->getId());

        $this->flashMessage('Book has been deleted.', 'success');
        $this->redirect('Book:default');
    }

    protected function createComponentCoverForm(): Form
    {
        $form = new Form;

        $form->addUpload('cover', 'Upload cover:')
            ->setRequired(false)
            ->addRule($form::Image, 'Cover must be JPEG, PNG or GIF.')
            ->addRule($form::MaxFileSize, 'Maximum file size is 2 MB.', 1 * 1024 * 1024);

        $form->addHidden('id', (string) $this->getParameter('id'));
        $form->addSubmit('send', 'Upload');

        $form->onSuccess[] = $this->coverFormSucceeded(...);

        return $form;
    }

    public function coverFormSucceeded(Form $form, \stdClass $values): void
    {
        $book = $this->bookService->getById((int) $values->id);
        if (!$book) {
            $this->error('Book was not found.');
        }

        if ($values->cover->isOk()) {

            $image = Image::fromFile($values->cover->getTemporaryFile());
            $requiredWidth = 600;
            $requiredHeight = 800;

            if ($image->width !== $requiredWidth || $image->height !== $requiredHeight) {
                $form->addError("Cover must be exactly {$requiredWidth}x{$requiredHeight} pixels.");
                return;
            }

            $fileName = uniqid('book_', true) . '.' . $values->cover->getImageFileExtension();
            $uploadDir = __DIR__ . '/../../../www/uploads/books/';
            $filePath = $uploadDir . $fileName;

            $values->cover->move($filePath);

            $this->bookService->update($book, ['coverPath' => $fileName]);

            $this->flashMessage('Cover was uploaded.', 'success');
        } else {
            $this->flashMessage('Upload failed.', 'danger');
        }

        $this->redirect('this');
    }

    protected function createComponentSearchForm(): Form
    {
        $form = new Form;

        $form->addText('q', 'Search')
            ->setDefaultValue($this->getParameter('q') ?? '')
            ->setHtmlAttribute('placeholder', 'Hledat…');

        $form->addSubmit('send', 'Search')
            ->setHtmlAttribute('class', 'btn btn-primary ms-2');

        $form->onSuccess[] = function (Form $form, \stdClass $values): void {
            $this->redirect('Book:default', ['q' => $values->q]);
        };

        return $form;
    }

    public function actionAdd(): void
    {
        $this->requireAdmin();
    }

    private function requireAdmin(): void
    {
        if (!$this->getUser()->isInRole('admin')) {
            $this->error('You are not authorized to perform this action.', 403);
        }
    }

}