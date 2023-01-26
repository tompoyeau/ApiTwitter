<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Tweet;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $faker = Faker\Factory::create('fr_FR');

            $tweet = new Tweet();
            $tweet->setTexte($faker->text());
            $tweet->setDate($faker->dateTimeBetween('-1 year'));

            $manager->persist($tweet);

            $user = new User();
            $user->setEmail($faker->email());
            $password = $this->hasher->hashPassword($user, $faker->password());
            $user->setPassword($password);
            $user->addTweet($tweet);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
