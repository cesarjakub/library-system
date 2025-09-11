<?php

namespace App\Presentation\Api\Loans;

use App\Core\Middleware\ApiKeyMiddleware;
use App\Model\Repositories\LoanRepository;
use App\Model\Services\BookService;
use App\Model\Services\LoanService;
use App\Model\Services\UserService;
use App\Presentation\Api\ApiPresenter;
use Nette\Application\Responses\JsonResponse;

class LoansPresenter extends ApiPresenter
{
    public function __construct(
        private LoanService $loanService,
        private BookService $bookService,
        private UserService $userService
    ){}

    protected function startup(): void
    {
        $this->addMiddleware(new ApiKeyMiddleware($this, 'tajny-apikey-124'));
        parent::startup();
    }

    public function actionList(): void
    {
        $loans = array_map(fn($loan) => [
            'id' => $loan->getId(),
            'book' => $loan->getBook()->getId(),
            'user' => $loan->getUser()->getId(),
            'loanedAt' => $loan->getLoanedAt()?->format('Y-m-d'),
            'returnedAt' => $loan->getReturnedAt()?->format('Y-m-d'),
        ], $this->loanService->getAll());

        $this->sendResponse(new JsonResponse($loans));
    }

    public function actionDetail(int $id): void
    {
        $loan = $this->loanService->getById($id);

        if (!$loan)
        {
            $this->getHttpResponse()->setCode(404);
            $this->sendResponse(new JsonResponse(['error' => 'Not found']));
        }

        $loanDetail = [
            'id' => $loan->getId(),
            'book' => [
                'id' => $loan->getBook()->getId(),
                'title' => $loan->getBook()->getTitle(),
            ],
            'user' => [
                'id' => $loan->getUser()->getId(),
                'email' => $loan->getUser()->getEmail(),
            ],
            'loanedAt' => $loan->getLoanedAt()?->format('Y-m-d'),
            'returnedAt' => $loan->getReturnedAt()?->format('Y-m-d'),
        ];

        $this->sendResponse(new JsonResponse($loanDetail));
    }

    public function actionReturn(int $id): void
    {
        $loan = $this->loanService->getById($id);

        if (!$loan) {
            $this->getHttpResponse()->setCode(404);
            $this->sendResponse(new JsonResponse(['error' => 'Not found']));
        }
        $this->loanService->markReturned($loan);

        $loanArray = [
            'id' => $loan->getId(),
            'returnedAt' => $loan->getReturnedAt()?->format('Y-m-d'),
        ];

        $this->sendResponse(new JsonResponse($loanArray));
    }

    public function actionCreate(): void
    {
        $data = json_decode($this->getHttpRequest()->getRawBody(), true);
        if (!isset($data['bookId'], $data['userId'])) {
            $this->getHttpResponse()->setCode(400);
            $this->sendResponse(new JsonResponse(['error' => 'Invalid input']));
        }

        $book = $this->bookService->getById((int)$data['bookId']);
        $user = $this->userService->getById((int)$data['userId']);
        if (!$book || !$user) {
            $this->getHttpResponse()->setCode(404);
            $this->sendResponse(new JsonResponse(['error' => 'Book or user not found']));
        }

        $loan = $this->loanService->create($user, $book);

        $loanDetail = [
            'id' => $loan->getId(),
            'book' => [
                'id' => $loan->getBook()->getId(),
                'title' => $loan->getBook()->getTitle(),
            ],
            'user' => [
                'id' => $loan->getUser()->getId(),
                'email' => $loan->getUser()->getEmail(),
            ],
            'loanedAt' => $loan->getLoanedAt()?->format('Y-m-d'),
            'returnedAt' => $loan->getReturnedAt()?->format('Y-m-d'),
        ];

        $this->getHttpResponse()->setCode(201);
        $this->sendResponse(new JsonResponse($loanDetail));
    }

    public function actionDelete(int $id): void
    {
        $loan = $this->loanService->getById($id);
        if (!$loan) {
            $this->getHttpResponse()->setCode(404);
            $this->sendResponse(new JsonResponse(['error' => 'Not found']));
        }

        $this->loanService->delete($loan);

        $this->getHttpResponse()->setCode(204);
    }
}