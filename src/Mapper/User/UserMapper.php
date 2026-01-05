<?php

namespace App\Mapper\User;

use App\DTO\User\Output\RegisterUserOutput;
use App\Entity\User;

class UserMapper
{
    public function toOutput(User $user): RegisterUserOutput
    {
        $response = new RegisterUserOutput();
        $response->id = $user->getId();
        $response->email = $user->getEmail();
        $response->phone = $user->getPhone();
        $response->createdAt = $user->getCreatedAt()->format('Y-m-d H:i:s');

        return $response;
    }
}

