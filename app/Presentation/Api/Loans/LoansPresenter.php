<?php

namespace App\Presentation\Api\Loans;

use App\Core\Middleware\ApiKeyMiddleware;
use App\Model\Services\LoanService;
use App\Presentation\Api\ApiPresenter;
use Nette\Application\Responses\JsonResponse;

class LoansPresenter extends ApiPresenter
{
    public function __construct(
        private LoanService $loanService
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

}