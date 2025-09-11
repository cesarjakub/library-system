<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;


        // API v1 routes
        $router->addRoute('api/v1/books', 'Api:Books:list');
        $router->addRoute('api/v1/books/<id>', 'Api:Books:detail');
        $router->addRoute('api/v1/loans', 'Api:loansList');
        $router->addRoute('api/v1/loans/<id>', 'Api:loanDetail');
        $router->addRoute('api/v1/loans/<id>/return', 'Api:loanReturn');

        // Default catch-all pro klasické presentery
        $router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
		return $router;
	}
}
