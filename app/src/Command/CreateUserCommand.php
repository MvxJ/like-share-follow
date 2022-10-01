<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'This command allow you to create user.',
)]
class CreateUserCommand extends Command
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private UserRepository $userRepository;

    public function __construct(
        string $name = null,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User e-mail')
            ->addArgument('password', InputArgument::OPTIONAL, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the user password: ');
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = empty($input->getArgument('password')) ?
            $helper->ask($input, $output, $question) : $input->getArgument('password');

        if ($password === null) {
            $io->error('Password cant be null');

            return Command::FAILURE;
        }

        try {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
            $this->userRepository->add($user, true);

            $io->success('User with e-mail ' . $email . ' was successfully created');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error('An exception occured during execution of command: ' . $exception->getMessage());

            return Command::FAILURE;
        }
    }
}
