<?php
declare(strict_types=1);

namespace Presentation\Book;

use App\Presentation\Book\BookPresenter;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class BookFormTest extends TestCase
{
    public function testEmptyTitleTriggersValidationError(): void
    {
        $presenter = new class extends BookPresenter {
            public function __construct() {}
            protected function startup(): void
            {}
        };

        $form = $presenter->getComponent('bookForm');

        $form->setDefaults([
            'title' => '',
            'author' => 'Harper Lee',
            'year' => 2020,
            'isbn' => '9780618644447',
        ], true);

        Assert::false($form->isValid());
        Assert::true($form['title']->hasErrors());
    }
}
(new BookFormTest())->run();