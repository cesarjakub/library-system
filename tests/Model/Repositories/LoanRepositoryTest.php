<?php
declare(strict_types=1);

namespace Model\Repositories;

use App\Model\Entities\Book;
use App\Model\Entities\Loan;
use App\Model\Entities\User;
use App\Model\Services\LoanService;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class LoanRepositoryTest extends TestCase
{
    private LoanService $loanServiceMock;

    protected function setUp(): void
    {
        $this->loanServiceMock = Mockery::mock(LoanService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateLoan(): void
    {
        $user = Mockery::mock(User::class);
        $book = Mockery::mock(Book::class);
        $loan = new Loan($user, $book);

        $this->loanServiceMock
            ->shouldReceive('create')
            ->with($user, $book)
            ->once()
            ->andReturn($loan);

        $result = $this->loanServiceMock->create($user, $book);

        Assert::same($loan, $result);
        Assert::same($user, $result->getUser());
        Assert::same($book, $result->getBook());
    }
}
(new LoanRepositoryTest())->run();