<?php

namespace App\DTO\Api\Output;

use Symfony\Component\Serializer\Annotation\Groups;

class LoginUserOutput
{
    #[Groups(['user:read'])]
    public string $accessToken;

    #[Groups(['user:read'])]
    public int $expiresIn;

    #[Groups(['user:read'])]
    public array $user;
}