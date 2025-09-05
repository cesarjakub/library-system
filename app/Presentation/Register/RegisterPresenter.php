<?php
declare(strict_types=1);

namespace App\Presentation\Register;

use App\Model\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class RegisterPresenter extends Presenter
{
    private EntityRepository $userRepo;

    public function __construct(EntityManagerInterface $em){
        $this->userRepo = $em->getRepository(User::class);
    }

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
        if ($this->userRepo->findOneBy(['email' => $values->email])) {
            $this->flashMessage('A user with this email already exists.', 'error');
            return;
        }

        $user = new User($values->email, $values->password);

        $this->userRepo->saveBook($user);

        $this->flashMessage('Registration successful', 'success');
        $this->redirect('Sign:in');
    }
}