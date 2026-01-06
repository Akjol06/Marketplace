<?php

namespace App\DataFixtures\User;

use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword($this->hasher->hashPassword($user, 'password123'));
        $manager->persist($user);

        $refreshToken = new RefreshToken();
        $refreshToken->setToken(Uuid::v4()->toRfc4122());
        $refreshToken->setUser($user);
        $refreshToken->setExpiresAt(new \DateTimeImmutable('+30 days'));
        $refreshToken->setRevoked(false);
        $manager->persist($refreshToken);

        $manager->flush();

        $this->addReference('user_example', $user);
        $this->addReference('refresh_example', $refreshToken);
    }
}