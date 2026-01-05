<?php

namespace App\Service\Auth;

use App\DTO\Api\Input\LoginUserInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            throw new \Exception('Invalid credentials');
        }

        return $user;
    }
}