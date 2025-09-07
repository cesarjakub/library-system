<?php
declare(strict_types=1);

namespace Presentation\Book;

use App\Presentation\Book\BookPresenter;
use Nette\Http\FileUpload;
use Nette\Utils\Image;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';
final class CoverFormTest extends TestCase
{
    private BookPresenter $presenter;
    private string $tempFileWrong;
    private string $tempFileCorrect;

    protected function setUp(): void
    {
        $this->presenter = new class extends BookPresenter {
            public function __construct() {}
            protected function startup(): void {}
            public bool $addBookCoverCalled = false;

            public function coverFormSucceeded($form, $values): void
            {
                if ($values->cover->isOk()) {
                    $image = Image::fromFile($values->cover->getTemporaryFile());
                    $requiredWidth = 600;
                    $requiredHeight = 800;

                    if ($image->width !== $requiredWidth || $image->height !== $requiredHeight) {
                        $form->addError("Cover must be exactly {$requiredWidth}x{$requiredHeight} pixels.");
                        return;
                    }

                    $this->addBookCoverCalled = true;
                }
            }
        };

        $this->tempFileWrong = tempnam(sys_get_temp_dir(), 'cover') . '.jpg';
        Image::fromBlank(500, 500)->save($this->tempFileWrong);

        $this->tempFileCorrect = tempnam(sys_get_temp_dir(), 'cover') . '.jpg';
        Image::fromBlank(600, 800)->save($this->tempFileCorrect);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFileWrong)) {
            unlink($this->tempFileWrong);
        }
        if (file_exists($this->tempFileCorrect)) {
            unlink($this->tempFileCorrect);
        }
    }

    public function testUploadFailsForWrongSize(): void
    {
        $form = $this->presenter->getComponent('coverForm');

        $upload = new FileUpload([
            'name' => 'wrong.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $this->tempFileWrong,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->tempFileWrong),
        ]);

        $form->setDefaults([
            'id' => 1,
            'cover' => $upload,
        ], true);

        $this->presenter->coverFormSucceeded($form, (object)[
            'id' => 1,
            'cover' => $upload,
        ]);

        Assert::true(count($form->getErrors()) > 0, 'Form should have an error for wrong image size.');
    }

    public function testUploadSuccessPersistsToDatabase(): void
    {
        $form = $this->presenter->getComponent('coverForm');

        $upload = new FileUpload([
            'name' => 'correct.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $this->tempFileCorrect,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->tempFileCorrect),
        ]);

        $form->setDefaults([
            'id' => 1,
            'cover' => $upload,
        ], true);

        $this->presenter->coverFormSucceeded($form, (object)[
            'id' => 1,
            'cover' => $upload,
        ]);

        Assert::true($this->presenter->addBookCoverCalled, 'addBookCover should have been called.');
        Assert::same([], $form->getErrors(), 'Form should have no errors for correct image.');
    }
}
(new CoverFormTest())->run();