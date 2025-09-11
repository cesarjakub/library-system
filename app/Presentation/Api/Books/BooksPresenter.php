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
        $apiKey = getenv('API_KEY_BOOKS');
        $this->addMiddleware(new ApiKeyMiddleware($this, $apiKey));
        parent::startup();
    }

    public function actionDefault(): void
    {
        $method = $this->getHttpRequest()->getMethod();

        if ($method === 'GET') {
            $this->actionList();
        } elseif ($method === 'POST') {
            $this->actionCreate();
        } else {
            $this->getHttpResponse()->setCode(405);
            $this->sendResponse(new JsonResponse(['error' => 'Method not allowed']));
        }
    }

    public function actionDetailMaster(int $id): void
    {
        $method = $this->getHttpRequest()->getMethod();

        if ($method === 'GET') {
            $this->actionDetail($id);
        } elseif ($method === 'PUT') {
            $this->actionUpdate($id);
        } elseif ($method === 'DELETE') {
            $this->actionDelete($id);
        } else {
            $this->getHttpResponse()->setCode(405);
            $this->sendResponse(new JsonResponse(['error' => 'Method not allowed']));
        }
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

    public function actionCreate(): void
    {
        $data = json_decode($this->getHttpRequest()->getRawBody(), true);

        if (!isset($data['title'], $data['author'], $data['year'], $data['isbn'])) {
            $this->getHttpResponse()->setCode(400);
            $this->sendResponse(new JsonResponse(['error' => 'Invalid input']));
        }

        $book = $this->bookService->create(
            $data['title'],
            $data['author'],
            (int)$data['year'],
            $data['isbn'],
        );

        $this->getHttpResponse()->setCode(201);
        $this->sendResponse(new JsonResponse([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'year' => $book->getYear(),
            'isbn' => $book->getIsbn()
        ]));
    }

    public function actionDelete(int $id): void
    {
        $book = $this->bookService->getById($id);
        if (!$book) {
            $this->getHttpResponse()->setCode(404);
            $this->sendResponse(new JsonResponse(['error' => 'Not found']));
        }

        $this->bookService->delete($book);

        $this->getHttpResponse()->setCode(204);
    }

    public function actionUpdate(int $id): void
    {
        $book = $this->bookService->getById($id);
        if (!$book) {
            $this->getHttpResponse()->setCode(404);
            $this->sendResponse(new JsonResponse(['error' => 'Not found']));
        }

        $data = json_decode($this->getHttpRequest()->getRawBody(), true);
        if (!is_array($data)) {
            $this->getHttpResponse()->setCode(400);
            $this->sendResponse(new JsonResponse(['error' => 'Invalid input']));
        }

        $this->bookService->update($book, $data);

        $this->getHttpResponse()->setCode(200);
        $this->sendResponse(new JsonResponse([
            'id'     => $book->getId(),
            'title'  => $book->getTitle(),
            'author' => $book->getAuthor(),
            'year'   => $book->getYear(),
            'isbn'   => $book->getIsbn(),
            'coverPath' => $book->getCoverPath()
        ]));
    }

}