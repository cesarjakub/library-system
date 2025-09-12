<?php

namespace App\Model\Services;

use App\Model\Entities\Book;
use App\Model\Entities\User;
use Nette\Mail\Mailer;
use Nette\Mail\Message;

class EmailNotificationService
{
    public function __construct(
        private Mailer $mailer,
    ){}

    public function sendLoanNotification(User $user, Book $book): void
    {
        $message = new Message();
        $message->setFrom('library@example.com')
            ->addTo($user->getEmail())
            ->setSubject('Book Loan Confirmation')
            ->setBody(
                "Hello {$user->getEmail()},\n\n".
                "You have successfully borrowed the book '{$book->getTitle()}'.\n".
                "Please return it within 30 days.\n\n".
                "Your Library"
            );

        $this->mailer->send($message);
    }

    public function sendOverdueNotification(User $user, Book $book): void
    {
        $message = new Message();
        $message->setFrom('library@example.com')
            ->addTo($user->getEmail())
            ->setSubject('Notification: Overdue Book')
            ->setBody(
                "Hello {$user->getEmail()},\n\n".
                "The book '{$book->getTitle()}' has been borrowed for more than 30 days.\n".
                "Please return it as soon as possible.\n\n".
                "Your Library"
            );

        $this->mailer->send($message);
    }
}