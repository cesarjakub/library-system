<?php

namespace App\Presentation\Api;

use App\Core\Middleware\IAuthMiddleware;
use Nette\Application\UI\Presenter;

abstract class ApiPresenter extends Presenter
{
    private array $middlewares = [];

    protected function startup(): void
    {
        parent::startup();
        foreach ($this->middlewares as $middleware) {
            $middleware->authenticate();
        }
    }

    protected function addMiddleware(IAuthMiddleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}