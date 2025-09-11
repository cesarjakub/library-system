<?php

namespace App\Core\Middleware;

interface IAuthMiddleware
{
    public function authenticate(): void;
}