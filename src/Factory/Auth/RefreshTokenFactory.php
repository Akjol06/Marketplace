<?php

namespace App\Factory\Auth;

use App\Entity\RefreshToken;
use App\Entity\User;
use Symfony\Component\Uid\Uuid;

class RefreshTokenFactory
{
    public function create(User $user): RefreshToken
    {
        return (new RefreshToken())
            ->setToken(Uuid::v4()->toRfc4122())
            ->setUser($user)
            ->setExpiresAt(new \DateTimeImmutable('+30 days'))
            ->setRevoked(false);
    }
}