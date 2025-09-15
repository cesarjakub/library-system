<?php
declare(strict_types=1);

namespace App\Presentation\Loan;

use Nette\Utils\Paginator;
use App\Model\Services\BookService;
use App\Model\Services\EmailNotificationService;
use App\Model\Services\LoanService;
use App\Model\Services\UserService;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class LoanPresenter extends Presenter
{
    public function __construct(
        private BookService $bookService,
        private UserService $userService,
        private LoanService $loanService,
        private EmailNotificationService $emailNotificationService,
    ){}

    public function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isInRole('admin')) {
            $this->error('You are not authorized to access this page.', 403);
        }
    }

    public function renderDefault(int $page = 1): void
    {
        $itemsPerPage = 10;
        $totalItems = $this->loanService->getCount();

        $paginator = new Paginator;
        $paginator->setItemCount($totalItems);
        $paginator->setItemsPerPage($itemsPerPage);
        $paginator->setPage($page);

        $loans = $this->loanService->getPage($paginator->getOffset(), $paginator->getItemsPerPage());

        $this->template->loans = $loans;
        $this->template->paginator = $paginator;
    }

    protected function createComponentLoanForm(): Form
    {
        $form = new Form;

        $users = $this->userService->getAll();
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[$user->getId()] = $user->getEmail();
        }

        $books = $this->bookService->getAll();
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
        $user = $this->userService->getById($values->user);
        $book = $this->bookService->getById($values->book);

        $this->loanService->create($user, $book);
        $this->emailNotificationService->sendLoanNotification($user, $book);

        $this->flashMessage('Loan created.', 'success');
        $this->redirect('Loan:default');
    }

    public function handleReturn(int $id): void
    {
        $loan = $this->loanService->getById($id);
        if (!$loan) {
            $this->error('Loan not found.');
        }

        $this->loanService->markReturned($loan);

        $this->flashMessage('Book marked as returned.', 'success');
        $this->redirect('this');
    }

}
