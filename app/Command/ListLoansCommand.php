<?php

namespace App\Command;
use App\Model\Entities\Loan;
use App\Model\Services\LoanService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'library:loans')]
class ListLoansCommand extends Command
{
    public function __construct(
        private LoanService $loanService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('user', null, InputOption::VALUE_REQUIRED, 'User ID to filter loans');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $input->getOption('user');

        $loans = $this->loanService->getActiveLoans();

        if ($userId) {
            $loans = array_filter($loans, fn($loan) => $loan->getUser()->getId() == $userId);
        }

        if (empty($loans)) {
            $output->writeln('No active loans found.');
            return Command::SUCCESS;
        }

        foreach ($loans as $loan) {
            $output->writeln(sprintf(
                'User %s has borrowed the book "%s" (ISBN: %s)',
                $loan->getUser()->getEmail(),
                $loan->getBook()->getTitle(),
                $loan->getBook()->getIsbn()
            ));
        }

        return Command::SUCCESS;
    }
}