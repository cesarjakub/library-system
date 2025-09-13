<?php

namespace Presentation\Api;

use App\Model\Services\BookService;
use App\Model\Services\LoanService;
use App\Model\Services\UserService;
use App\Presentation\Api\Loans\LoansPresenter;
use DateTime;
use Mockery;
use Nette\Application\Responses\JsonResponse;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

class LoansPresenterTest extends TestCase
{
    public function testActionListMultipleChecks(): void
    {
        $book = new class {
            public function getId() { return 1; }
            public function getTitle() { return 'Book Title'; }
        };

        $user = new class {
            public function getId() { return 10; }
            public function getEmail() { return 'user@example.com'; }
        };

        $loan = new class($book, $user) {
            private $book;
            private $user;
            public function __construct($book, $user) { $this->book = $book; $this->user = $user; }
            public function getId() { return 100; }
            public function getBook() { return $this->book; }
            public function getUser() { return $this->user; }
            public function getLoanedAt() { return new DateTime('2025-09-13'); }
            public function getReturnedAt() { return null; }
        };

        $loanService = Mockery::mock(LoanService::class);
        $loanService->shouldReceive('getAll')->once()->andReturn([$loan]);

        $bookService = Mockery::mock(BookService::class);
        $userService = Mockery::mock(UserService::class);

        $presenter = new class($loanService, $bookService, $userService) extends LoansPresenter {
            public ?JsonResponse $jsonResponse = null;
            public function sendResponse($response): void { $this->jsonResponse = $response; }
        };

        $presenter->actionList();

        Assert::true($presenter->jsonResponse instanceof JsonResponse, 'Response must be JsonResponse');
    }
}
(new LoansPresenterTest())->run();