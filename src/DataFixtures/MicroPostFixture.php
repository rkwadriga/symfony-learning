<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MicroPostFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            if ($user->hasRole('ROLE_ADMIN')) {
                continue;
            }

            $postsCount = rand(1, 10);
            for ($j = 0; $j < $postsCount; $j++) {
                $post = (new MicroPost())
                    ->setTitle($faker->sentence)
                    ->setText($faker->text)
                    ->setCreatedAt(new DateTimeImmutable($faker->dateTimeThisMonth->format('Y-m-d H:i:s')))
                    ->setOwner($user)
                ;

                $commentsCount = rand(1, 10);
                for ($i = 0; $i < $commentsCount; $i++) {
                    $comment = (new Comment())
                        ->setText($faker->text)
                        ->setCreatedAt(new DateTimeImmutable($faker->dateTimeThisYear->format('Y-m-d H:i:s')))
                    ;
                    $post->addComment($comment);
                }

                $manager->persist($post);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }
}
