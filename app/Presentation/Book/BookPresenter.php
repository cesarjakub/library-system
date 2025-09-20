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

    public function renderDefault(?string $q = null, ?string $author = null, ?int $year_from = null, ?int $year_to = null, int $page = 1): void
    {
        $itemsPerPage = 10;

        $paginator = new Paginator;
        $paginator->setItemsPerPage($itemsPerPage);
        $paginator->setPage($page);

        $searchParams = [
            'query' => $q,
            'author' => $author,
            'year_from' => $year_from,
            'year_to' => $year_to,
        ];

        $totalItems = $this->bookIndexer->countSearchResultsWithFilters($searchParams);
        $paginator->setItemCount($totalItems);

        $from = $paginator->getOffset();
        $ids = $this->bookIndexer->searchWithFilters($searchParams, $from, $itemsPerPage);
        $books = $this->bookService->getByIds($ids);

        $activeFilters = [];
        if ($q) {
            $activeFilters['Search'] = $q;
        }
        if ($author) {
            $activeFilters['Author'] = $author;
        }
        if ($year_from) {
            $activeFilters['Year from'] = $year_from;
        }
        if ($year_to) {
            $activeFilters['Year to'] = $year_to;
        }

        $this->template->activeFilters = $activeFilters;
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

        $this->bookIndexer->delete($book->getId());
        $this->bookService->delete($book);

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

    protected function createComponentFilterForm(): Form
    {
        $authors = $this->bookService->getAllAuthors();
        [$minYear, $maxYear] = $this->bookService->getYearRange();

        $start = floor($minYear / 10) * 10;
        $end   = ceil($maxYear / 10) * 10;

        $jump = [];
        for ($y = $start; $y <= $end; $y += 30) {
            $jump[$y] = $y;
        }

        $form = new Form;
        $form->addText('q', 'Search');
        $form->addSelect('author', 'Author:', $authors)
            ->setPrompt('All authors');
        $form->addSelect('year_from', 'Year from:', $jump)
            ->setPrompt('From any');
        $form->addSelect('year_to', 'Year to:', $jump)
            ->setPrompt('To any');
        $form->addSubmit('send', 'Filter');

        $form->onSuccess[] = $this->filterFormSucceeded(...);

        return $form;
    }

    public function filterFormSucceeded(Form $form, \stdClass $values)
    {
        $this->redirect('Book:default', [
            'q' => $values->q ?: null,
            'author' => $values->author ?: null,
            'year_from' => $values->year_from ?: null,
            'year_to' => $values->year_to ?: null,
        ]);
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