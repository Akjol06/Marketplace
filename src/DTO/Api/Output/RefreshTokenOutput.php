<?php

namespace App\DTO\Api\Output;

use Symfony\Component\Serializer\Annotation\Groups;

class RefreshTokenOutput
{   
    #[Groups(['auth:read'])]
    public string $refreshToken;

    #[Groups(['user:read'])]
    public string $accessToken;

    #[Groups(['user:read'])]
    public int $expiresIn;
}