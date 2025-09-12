<?php

namespace Model\Services;

use App\Model\Entities\Book;
use App\Model\Entities\User;
use App\Model\Services\EmailNotificationService;
use Mockery;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class EmailNotificationServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testSendLoanNotification(): void
    {
        $mockMailer = Mockery::mock(Mailer::class);

        $mockMailer
            ->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function (Message $message) {
                Assert::same(['library@example.com' => null], $message->getHeader('From'));
                Assert::same(['test@example.com' => null], $message->getHeader('To'));
                Assert::contains('Clean Code', $message->getBody());
                return true;
            }));

        $service = new EmailNotificationService($mockMailer);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getEmail')->andReturn('test@example.com');

        $book = Mockery::mock(Book::class);
        $book->shouldReceive('getTitle')->andReturn('Clean Code');

        $service->sendLoanNotification($user, $book);
    }
}
(new EmailNotificationServiceTest())->run();