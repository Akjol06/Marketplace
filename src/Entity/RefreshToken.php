<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\DTO\Api\Input\RefreshTokenInput;
use App\DTO\Api\Output\RefreshTokenOutput;
use App\Helper\EndpointRoutes;
use App\Repository\RefreshTokenRepository;
use App\State\Auth\RefreshTokenProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: self::REFRESH_TOKEN_URL, 
            name: EndpointRoutes::TOKEN_REFRESH_POST,
            input: RefreshTokenInput::class,
            output: RefreshTokenOutput::class,
            processor: RefreshTokenProcessor::class,
        )
    ]
)]
class RefreshToken
{
    public const REFRESH_TOKEN_URL = '/token/refresh';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $token;

    #[ORM\ManyToOne(inversedBy: 'refreshTokens')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column]
    private bool $revoked = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function isRevoked(): ?bool
    {
        return $this->revoked;
    }

    public function setRevoked(bool $revoked): static
    {
        $this->revoked = $revoked;

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }
}
