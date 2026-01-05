<?php

namespace App\Service\Auth;

use App\DTO\Api\Output\RefreshTokenOutput;
use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RefreshTokenService
{
    public function __construct(
        private EntityManagerInterface $em,
        private JwtTokenService $jwt
    ) {}

    public function refresh(string $token): RefreshTokenOutput
    {
        $repo = $this->em->getRepository(RefreshToken::class);
        /** @var RefreshToken|null $refreshToken */
        $refreshToken = $repo->findOneBy(['token' => $token]);

        if (!$refreshToken) {
            throw new \Exception('Invalid refresh token');
        }

        if ($refreshToken->isRevoked() || $refreshToken->isExpired()) {
            throw new \Exception('Refresh token expired or revoked');
        }

        /** @var User $user */
        $user = $refreshToken->getUser();

        $refreshToken->revoke();
        $this->em->flush();

        $newRefreshToken = new RefreshToken();
        $newRefreshToken->setToken(\Symfony\Component\Uid\Uuid::v4()->toRfc4122());
        $newRefreshToken->setUser($user);
        $newRefreshToken->setExpiresAt(new \DateTimeImmutable('+30 days'));
        $newRefreshToken->setRevoked(false);

        $this->em->persist($newRefreshToken);
        $this->em->flush();

        $output = new RefreshTokenOutput();
        $output->accessToken = $this->jwt->generate($user);
        $output->refreshToken = $newRefreshToken->getToken();
        $output->expiresIn = 3600;

        return $output;
    }

    public function validate(string $token): RefreshToken
    {
        $repo = $this->em->getRepository(RefreshToken::class);

        $refreshToken = $repo->findOneBy(['token' => $token]);

        if (!$refreshToken || $refreshToken->isRevoked() || $refreshToken->isExpired()) {
            throw new \Exception('Invalid refresh token');
        }

        return $refreshToken;
    }

    public function revoke(RefreshToken $refreshToken): void
    {
        $refreshToken->revoke();
        $this->em->flush();
    }
}