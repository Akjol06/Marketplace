<?php

namespace App\State\Auth;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Api\Output\LoginUserOutput;
use App\Service\Auth\JwtTokenService;
use App\Service\Auth\LoginUserService;

class LoginUserProcessor implements ProcessorInterface
{
    public function __construct(
        private LoginUserService $login,
        private JwtTokenService $jwt
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): LoginUserOutput
    {
        $user = $this->login->login($data);
        
        $refreshToken = $this->login->createRefreshToken($user);

        $out = new LoginUserOutput();
        $out->accessToken = $this->jwt->generate($user);
        $out->refreshToken = $refreshToken->getToken();
        $out->expiresIn = 3600;
        $out->user = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        return $out;
    }
}