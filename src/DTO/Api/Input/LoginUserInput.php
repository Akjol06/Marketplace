<?php

namespace App\DTO\Api\Input;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class LoginUserInput
{
    #[Assert\NotBlank]
    #[Groups(['user:write'])]
    public string $identifier;

    #[Assert\NotBlank]
    #[Groups(['user:write'])]
    public string $password;
}