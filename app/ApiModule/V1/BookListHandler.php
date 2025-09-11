<?php

namespace App\ApiModule\V1;

use App\Model\Services\BookService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Response;

class BookListHandler implements RequestHandlerInterface
{
    public function __construct(
        private BookService $bookService
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $books = $this->bookService->getAll();

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($books, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }
}