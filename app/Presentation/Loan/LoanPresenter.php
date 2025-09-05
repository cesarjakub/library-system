<?php
declare(strict_types=1);

namespace App\Presentation\Loan;

use App\Model\Entities\Book;
use App\Model\Entities\Loan;
use App\Model\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class LoanPresenter extends Presenter
{
    private EntityRepository $loanRepo;
    private EntityRepository $bookRepo;
    private EntityRepository $userRepo;
    public function __construct(EntityManagerInterface $em)
    {
        $this->loanRepo = $em->getRepository(Loan::class);
        $this->bookRepo = $em->getRepository(Book::class);
        $this->userRepo = $em->getRepository(User::class);
    }

    public function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isInRole('admin')) {
            $this->error('You are not authorized to access this page.', 403);
        }
    }

    public function renderDefault(): void
    {
        $this->template->loans = $this->loanRepo->findActiveLoans();
    }

    protected function createComponentLoanForm(): Form
    {
        $form = new Form;

        $users = $this->userRepo->findAll();
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[$user->getId()] = $user->getEmail();
        }

        $books = $this->bookRepo->findAll();
        $bookOptions = [];
        foreach ($books as $book) {
            $bookOptions[$book->getId()] = $book->getTitle();
        }

        $form->addSelect('user', 'User:', $userOptions)
            ->setPrompt('Select user')
            ->setRequired();

        $form->addSelect('book', 'Book:', $bookOptions)
            ->setPrompt('Select book')
            ->setRequired();

        $form->addSubmit('send', 'Loan');
        $form->onSuccess[] = [$this, 'loanFormSucceeded'];

        return $form;
    }

    public function loanFormSucceeded(Form $form, \stdClass $values): void
    {
        $user = $this->userRepo->find($values->user);
        $book = $this->bookRepo->find($values->book);

        $this->loanRepo->createLoan($user, $book);

        $this->flashMessage('Loan created.', 'success');
        $this->redirect('Loan:default');
    }

    public function handleReturn(int $id): void
    {
        $loan = $this->loanRepo->find($id);
        if (!$loan) {
            $this->error('Loan not found.');
        }

        $this->loanRepo->markReturned($loan);

        $this->flashMessage('Book marked as returned.', 'success');
        $this->redirect('this');
    }

}
