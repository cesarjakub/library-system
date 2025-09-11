<?php
declare(strict_types=1);

namespace App\ApiModule\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('api-token');

        if ($token !== 'api-key-secret') {
            return new Response(
                401,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );
        }

        return $handler->handle($request);
    }
}