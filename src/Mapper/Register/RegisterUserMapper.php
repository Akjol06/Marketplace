<?php

namespace App\Mapper\Register;

use App\DTO\Api\Output\Register\RegisterUserOutput;
use App\Entity\User;

class RegisterUserMapper
{
    public function toOutput(User $user): RegisterUserOutput
    {
        $dto = new RegisterUserOutput();
        $dto->id = $user->getId();
        $dto->email = $user->getEmail();
        $dto->phone = $user->getPhone();
        $dto->createdAt = $user->getCreatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }
}