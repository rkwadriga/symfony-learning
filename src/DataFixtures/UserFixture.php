<?php

namespace App\DataFixtures;

use App\Entity\UserProfile;
use DateTimeImmutable;;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixture extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $password = '12345678';
        $faker = Factory::create();

        $adminProfile = (new UserProfile())
            ->setName('Admin')
            ->setBio('Admin')
            ->setCompany('Local')
            ->setDateOfBirth(
                (new DateTimeImmutable($faker->dateTimeBetween('-40 years', '-20 years')->format('Y-m-d H:i:s')))
            )
            ->setLocation("Kiev")
            ->setWebsiteUrl('http://localhost:8888/')
            ->setTwitterUserName($faker->userName)
        ;

        $admin = (new User())
            ->setEmail('admin.mail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setProfile($adminProfile)
        ;
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
        $manager->persist($admin);

        $roles = ['ROLE_USER', 'ROLE_COMMENTER', 'ROLE_EDITOR'];
        for ($i = 0; $i < 11; $i++) {
            $company = $faker->company;
            $webSite = str_replace(' ', '-', str_replace([',', '-'], '', strtolower($company)));
            if (rand(1, 10) > 7) {
                $role = $roles[rand(1, count($roles) - 1)];
            } else {
                $role = 'ROLE_USER';
            }

            $userProfile = (new UserProfile())
                ->setName($faker->firstName)
                ->setBio($faker->lastName)
                ->setCompany($company)
                ->setDateOfBirth(
                    (new DateTimeImmutable($faker->dateTimeBetween('-80 years', '-13 years')->format('Y-m-d H:i:s')))
                )
                ->setLocation($faker->city)
                ->setWebsiteUrl("https://{$webSite}.com")
                ->setTwitterUserName($faker->userName)
            ;

            $user = (new User())
                ->setEmail($faker->email)
                ->setRoles([$role])
                ->setCreatedAt(new DateTimeImmutable($faker->dateTimeThisMonth->format('Y-m-d H:i:s')))
                ->setProfile($userProfile)
            ;
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
