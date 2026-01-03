<?php

namespace App\Service\Register;

use App\DTO\Api\Input\Register\RegisterUserInput;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {}

    public function register(RegisterUserInput $data): User
    {
        if ($this->em->getRepository(User::class)->findOneBy(['email' => $data->email])) {
            throw new \Exception('User already exists');
        }

        $user = new User();
        $user->setEmail($data->email);
        $user->setPassword($this->hasher->hashPassword($user, $data->password));
        // $user->setRoles([UserRole::USER->value]);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}