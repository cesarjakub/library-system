<?php

namespace App\Command;

use App\Model\Services\LoanService;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'library:notify-overdue')]
class NotifyOverdueCommand extends Command
{
    public function __construct(
        private LoanService $loanService,
        private Mailer $mailer
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

            $message = new Message();
            $message->setFrom('jirka@gmail.com');
            $message->addTo($user->getEmail());
            $message->setSubject('Notification: Overdue Book');
            $message->setBody(
                    "Hello {$user->getEmail()},\n\n".
                    "The book '{$book->getTitle()}' has been borrowed for more than 30 days.\n".
                    "Please return it as soon as possible.\n\n".
                    "Your Library"
                );

            $this->mailer->send($message);

            $output->writeln(
                "Notification sent: {$user->getEmail()} – '{$book->getTitle()}'"
            );
        }

        $output->writeln('Done: all notifications have been sent.');

        return Command::SUCCESS;
    }
}