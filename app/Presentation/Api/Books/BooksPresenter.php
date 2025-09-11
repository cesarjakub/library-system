<?php

namespace App\Presentation\Api\Books;

use App\Core\Middleware\ApiKeyMiddleware;
use App\Model\Services\BookService;
use App\Presentation\Api\ApiPresenter;
use Nette\Application\Responses\JsonResponse;

class BooksPresenter extends ApiPresenter
{
    public function __construct(
        private BookService $bookService
    ){}

    protected function startup(): void
    {
        $this->addMiddleware(new ApiKeyMiddleware($this, 'tajny-apikey-123'));
        parent::startup();
    }

    public function actionList(): void
    {
        $books = array_map(fn($book) => [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'year' => $book->getYear(),
        ], $this->bookService->getAll());

        $this->sendResponse(new JsonResponse($books));
    }

    public function actionDetail(int $id): void
    {
        $book = $this->bookService->getById($id);

        if (!$book)
        {
            $this->getHttpResponse()->setCode(404);
            $this->sendResponse(new JsonResponse(['error' => 'Not found']));
        }

        $bookDetail = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'year' => $book->getYear(),
            'isbn' => $book->getIsbn(),
            'coverPath' => $book->getCoverPath(),
        ];

        $this->sendResponse(new JsonResponse($bookDetail));
    }
}