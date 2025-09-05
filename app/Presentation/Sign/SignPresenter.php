<?php
declare(strict_types=1);

namespace App\Presentation\Sign;


use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

final class SignPresenter extends Presenter
{
    public function __construct(
        private User $user
    ){}

    protected function startup(): void
    {
        parent::startup();

        if ($this->getAction() === 'in' && $this->getUser()->isLoggedIn()) {
            $this->flashMessage('You are already logged in.');
            $this->redirect('Book:default');
        }
    }

    protected function createComponentLoginForm(): Form
    {
        $form = new Form;
        $form->addEmail('email', 'Email:')
            ->setRequired('Please enter your email.');

        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter your password.');

        $form->addSubmit('send', 'Login');

        $form->onSuccess[] = $this->loginFormSucceeded(...);

        return $form;
    }

    private function loginFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->user->login($values->email, $values->password);
            $this->flashMessage('Login successful', 'success');
            $this->redirect('Book:default');
        } catch (AuthenticationException $e) {
            $this->flashMessage('Invalid credentials', 'error');
        }
    }

    public function actionOut(): void
    {
        $this->user->logout();
        $this->flashMessage('You have been logged out.');
        $this->redirect('Home:default');
    }
}