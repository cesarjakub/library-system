<?php
declare(strict_types=1);

namespace App\Core\Middleware;

use Nette\Application\UI\Presenter;
use Nette\Application\Responses\JsonResponse;

class ApiKeyMiddleware implements IAuthMiddleware
{
    public function __construct(
        private Presenter $presenter,
        private string $apiKey
    ){}

    public function authenticate(): void
    {
        $request = $this->presenter->getHttpRequest()->getHeader('X-Api-Token');
        if ($request !== $this->apiKey) {
            $this->presenter->getHttpResponse()->setCode(401);

            $response = new JsonResponse(['error' => 'Unauthorized']);
            $this->presenter->sendResponse($response);
        }
    }
}