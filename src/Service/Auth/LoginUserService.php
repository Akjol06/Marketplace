<?php

namespace App\Service\Auth;

use App\DTO\Api\Input\LoginUserInput;
use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Uid\Uuid;

class LoginUserService 
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {}

    public function login(LoginUserInput $data): User
    {
        $repo = $this->em->getRepository(User::class);

        $user = $repo->findOneBy(['email' => $data->identifier])
            ?? $repo->findOneBy(['phone' => $data->identifier]);

        if (!$user || !$this->hasher->isPasswordValid($user, $data->password)) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials');
        }

        return $user;
    }

    public function createRefreshToken(User $user): RefreshToken
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setToken(Uuid::v4()->toRfc4122());
        $refreshToken->setUser($user);
        $refreshToken->setExpiresAt(new \DateTimeImmutable('+30 days'));
        $refreshToken->setRevoked(false);

        $this->em->persist($refreshToken);
        $this->em->flush();

        return $refreshToken;
    }
}