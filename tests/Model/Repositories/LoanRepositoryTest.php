<?php
declare(strict_types=1);

namespace Model\Repositories;

use App\Model\Entities\Book;
use App\Model\Entities\Loan;
use App\Model\Entities\User;
use App\Model\Repositories\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class LoanRepositoryTest extends TestCase
{
    private LoanRepository $loanRepository;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->em = Mockery::mock(EntityManagerInterface::class);
        
        $this->loanRepository = new class($this->em) extends LoanRepository {
            public function __construct($em) {
                $this->_em = $em;
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateLoan(): void
    {
        $user = Mockery::mock(User::class);
        $book = Mockery::mock(Book::class);

        $realLoan = new Loan($user, $book);

        $persistedLoan = null;

        $this->em->shouldReceive('persist')
            ->once()
            ->with(Mockery::on(function ($loan) use (&$persistedLoan) {
                $persistedLoan = $loan;
                return $loan instanceof Loan;
            }));

        $this->em->shouldReceive('flush')
            ->once();

        $this->loanRepository->saveLoan($realLoan);

        Assert::type(Loan::class, $persistedLoan);
        Assert::same($user, $persistedLoan->getUser());
        Assert::same($book, $persistedLoan->getBook());
    }
}
(new LoanRepositoryTest())->run();