<?php

namespace App\Command;

use App\Model\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'library:seed-users')]
class SeedUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('count', InputArgument::REQUIRED, 'Number of users to generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = (int) $input->getArgument('count');

        if ($count <= 0) {
            $output->writeln('The number of users must be greater than 0.');
            return Command::FAILURE;
        }

        $faker = Factory::create();
        $created = 0;

        for ($i = 0; $i < $count; $i++) {
            $email = $faker->unique()->safeEmail();
            $plainPassword = $faker->password(8, 16);

            $user = new User($email, $plainPassword);

            $this->em->persist($user);
            $created++;

            $output->writeln(
                sprintf(
                    'Created user: %s with password "%s"',
                    $email,
                    $plainPassword
                )
            );
        }

        $this->em->flush();

        $output->writeln(sprintf('Done! %s users were created.', $created));

        return Command::SUCCESS;
    }
}
