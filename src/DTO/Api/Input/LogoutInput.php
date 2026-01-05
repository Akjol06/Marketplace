<?php

namespace App\DTO\Api\Input;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class LogoutInput
{
    #[Assert\NotBlank]
    #[Groups(['user:write'])]
    public string $refreshToken;
}