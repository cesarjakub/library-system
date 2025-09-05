<?php
declare(strict_types=1);

namespace App\Presentation\Register;

use App\Model\Services\UserService;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class RegisterPresenter extends Presenter
{

    public function __construct(
        private UserService $userService
    ){}

    protected function startup(): void
    {
        parent::startup();

        if ($this->getUser()->isLoggedIn()) {
            $this->flashMessage('You are already logged in.');
            $this->redirect('Book:default');
        }
    }

    protected function createComponentRegisterForm(): Form
    {
        $form = new Form;
        $form->addEmail('email', 'Email:')
            ->setRequired('Please enter your email.');

        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter a password.')
            ->addRule($form::MinLength, 'Password must be at least %d characters long', 6);

        $form->addPassword('password2', 'Repeat password:')
            ->setRequired('Please confirm your password.')
            ->addRule($form::Equal, 'Passwords do not match', $form['password']);

        $form->addSubmit('send', 'Register');

        $form->onSuccess[] = $this->registerFormSucceeded(...);

        return $form;
    }

    private function registerFormSucceeded(Form $form, \stdClass $values): void
    {
        if ($this->userService->getUserByEmail($values->email) != null) {
            $this->flashMessage('A user with this email already exists.', 'error');
            return;
        }

        $this->userService->create($values->email, $values->password);

        $this->flashMessage('Registration successful', 'success');
        $this->redirect('Sign:in');
    }
}