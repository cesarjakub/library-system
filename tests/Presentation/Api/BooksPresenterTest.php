<?php

namespace Presentation\Api;

use App\Model\Services\BookService;
use App\Presentation\Api\Books\BooksPresenter;
use Mockery;
use Nette\Application\Responses\JsonResponse;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

class BooksPresenterTest extends TestCase
{
    public function testActionListReturnsJson(): void
    {
        $bookService = Mockery::mock(BookService::class);
        $bookService->shouldReceive('getAll')
            ->once()
            ->andReturn([
                new class {
                    public function getId() { return 1; }
                    public function getTitle() { return 'Test Book'; }
                    public function getAuthor() { return 'Test Author'; }
                    public function getYear() { return 2025; }
                    public function getIsbn() { return '1234567890'; }
                    public function getCoverPath() { return '/cover.jpg'; }
                }
            ]);

        $presenter = new class($bookService) extends BooksPresenter {
            public ?JsonResponse $jsonResponse = null;

            public function sendResponse($response): void
            {
                $this->jsonResponse = $response;
            }
        };
        $presenter->actionList();
        Assert::true($presenter->jsonResponse instanceof JsonResponse);
    }
}
(new BooksPresenterTest())->run();