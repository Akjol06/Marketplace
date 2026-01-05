<?php

namespace App\Service\Auth;

use App\Entity\User;
use Firebase\JWT\JWT;

class JwtTokenService
{
    public function generate(User $user): string
    {
        return JWT::encode([
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'exp' => time() + 3600,
        ], $_ENV['JWT_SECRET'], 'HS256');
    }
}