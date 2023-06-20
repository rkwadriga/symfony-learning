<?php

namespace App\Command;

use App\Entity\User;
use DateTimeImmutable;
use Exception;
use App\Entity\UserProfile;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a user',
)]
class CreateUserCommand extends AbstractCommand
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null,  InputOption::VALUE_REQUIRED, 'User email (used as unique user identifier)')
            ->addOption('role', null,  InputOption::VALUE_REQUIRED, 'User role')
            ->addOption('password', null,  InputOption::VALUE_REQUIRED, 'User password')
            ->addOption('name', null,  InputOption::VALUE_OPTIONAL, 'User name')
            ->addOption('bio', null,  InputOption::VALUE_OPTIONAL, 'User bio')
            ->addOption('website', null,  InputOption::VALUE_OPTIONAL, 'User website')
            ->addOption('twitter_username', null,  InputOption::VALUE_OPTIONAL, 'User twitter user name')
            ->addOption('company', null,  InputOption::VALUE_OPTIONAL, 'User company')
            ->addOption('location', null,  InputOption::VALUE_OPTIONAL, 'User location')
            ->addOption('birthday', null,  InputOption::VALUE_OPTIONAL, 'User birth date (in format "Y-m-d")')
        ;
    }

    protected function exec(InputInterface $input, SymfonyStyle $io): int
    {
        if (!$this->validateInput($input, $io)) {
            return Command::FAILURE;
        }

        $profile = (new UserProfile())
            ->setName($input->getOption('name'))
            ->setBio($input->getOption('bio'))
            ->setWebsiteUrl($input->getOption('website'))
            ->setTwitterUserName($input->getOption('twitter_username'))
            ->setCompany($input->getOption('company'))
            ->setLocation($input->getOption('location'))
            ->setDateOfBirth($input->getOption('birthday'))
        ;

        $user = (new User())
            ->setEmail($input->getOption('email'))
            ->setRoles($input->getOption('role'))
            ->setCreatedAt(new DateTimeImmutable())
            ->setProfile($profile)
        ;
        $user->setPassword($this->passwordHasher->hashPassword($user, $input->getOption('password')));

        $this->userRepository->save($user, true);

        $io->success("User #{$user->getId()} successfully created!");

        return Command::SUCCESS;
    }

    private function validateInput(InputInterface $input, SymfonyStyle $io): bool
    {
        $email = $input->getOption('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('Invalid email format');

            return false;
        }

        $role = $input->getOption('role');
        if (!in_array($role, ['ROLE_GUEST', 'ROLE_USER', 'ROLE_ADMIN'])) {
            $io->error('Invalid role value');

            return false;
        }
        $roles = ['ROLE_GUEST'];
        if ($role === 'ROLE_ADMIN') {
            $roles[] = 'ROLE_USER';
        }
        $roles[] = $role;
        $input->setOption('role', $roles);

        $password = $input->getOption('password');
        if (strlen($password < 4)) {
            $io->error('Password mast have more than 3 symbols');

            return false;
        }

        $birthday = $input->getOption('birthday');
        if ($birthday !== null) {
            try {
                $input->setOption('birthday', new DateTimeImmutable($birthday));
            } catch (Exception $e) {
                $io->error('Invalid birthday format');

                return false;
            }
        }

        $website = $input->getOption('website');
        if ($website !== null && !filter_var($website, FILTER_VALIDATE_URL)) {
            $io->error('Invalid website url format');

            return false;
        }

        return true;
    }
}
