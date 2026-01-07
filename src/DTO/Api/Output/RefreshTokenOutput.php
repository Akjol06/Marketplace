<?php

namespace App\DTO\Api\Output;

use Symfony\Component\Serializer\Annotation\Groups;

class RefreshTokenOutput
{   
    #[Groups(['user:read'])]
    public string $refreshToken;

    #[Groups(['user:read'])]
    public string $accessToken;

    #[Groups(['user:read'])]
    public int $expiresIn;

    public static function from(string $accessToken, string $refreshToken, int $expiresIn = 3600): self
    {
        $output = new self();
        $output->accessToken = $accessToken;
        $output->refreshToken = $refreshToken;
        $output->expiresIn = $expiresIn;

        return $output;
    }
}