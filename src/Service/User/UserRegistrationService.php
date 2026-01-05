<?php

namespace App\Service\User;

use App\DTO\User\Input\RegisterUserInput;
use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository
    ) {
    }

    public function register(RegisterUserInput $request): User
    {
        if ($this->userRepository->findOneByEmail($request->email)) {
            throw new UserAlreadyExistsException($request->email);
        }

        $user = new User();
        $user->setEmail($request->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $request->password));

        if ($request->phone) {
            $user->setPhone($request->phone);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

