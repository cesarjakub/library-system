<?php

namespace App\Command;

use App\Model\Services\EmailNotificationService;
use App\Model\Services\LoanService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'library:notify-overdue')]
class NotifyOverdueCommand extends Command
{
    public function __construct(
        private LoanService $loanService,
        private EmailNotificationService $emailNotificationService
    ){
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $overdueLoans = $this->loanService->findOverdueLoans(30);

        if (!$overdueLoans) {
            $output->writeln('No overdue loans found.');
            return Command::SUCCESS;
        }


        foreach ($overdueLoans as $loan) {
            $user = $loan->getUser();
            $book = $loan->getBook();

            $this->emailNotificationService->sendOverdueNotification($user, $book);


            $output->writeln(
                "Notification sent: {$user->getEmail()} – '{$book->getTitle()}'"
            );
        }

        $output->writeln('Done: all notifications have been sent.');

        return Command::SUCCESS;
    }
}