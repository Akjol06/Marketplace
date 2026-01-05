<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\DTO\Api\Input\LoginUserInput;
use App\DTO\Api\Input\LogoutInput;
use App\DTO\Api\Input\RegisterUserInput;
use App\DTO\Api\Output\LoginUserOutput;
use App\DTO\Api\Output\RegisterUserOutput;
use App\Enum\UserRole;
use App\Helper\EndpointRoutes;
use App\State\Auth\LoginUserProcessor;
use App\Repository\UserRepository;
use App\State\Auth\LogoutProcessor;
use App\State\Auth\UserRegistrationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: self::USER_REGISTER_URL,
            name: EndpointRoutes::USER_REGISTER_POST,
            input: RegisterUserInput::class,
            output: RegisterUserOutput::class,
            processor: UserRegistrationProcessor::class,
        ),
        new Post(
            uriTemplate: self::USER_LOGIN_URL,
            name: EndpointRoutes::USER_LOGIN_POST,
            input: LoginUserInput::class,
            output: LoginUserOutput::class,
            processor: LoginUserProcessor::class,
        ),
        new Post(
            uriTemplate: self::USER_LOGOUT_URL,
            name: EndpointRoutes::USER_LOGOUT_POST,
            input: LogoutInput::class,
            output: false,
            processor: LogoutProcessor::class,
        )
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const USER_REGISTER_URL = '/users/register';
    public const USER_LOGIN_URL = '/login';
    public const USER_LOGOUT_URL = '/logout';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 20, unique: true, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $phone = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, RefreshToken>
     */
    #[ORM\OneToMany(targetEntity: RefreshToken::class, mappedBy: 'user')]
    private Collection $refreshTokens;

    public function __construct()
    {
        $this->roles = [UserRole::USER->value];
        $this->createdAt = new \DateTimeImmutable();
        $this->refreshTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = UserRole::USER->value;
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, RefreshToken>
     */
    public function getRefreshTokens(): Collection
    {
        return $this->refreshTokens;
    }

    public function addRefreshToken(RefreshToken $refreshToken): static
    {
        if (!$this->refreshTokens->contains($refreshToken)) {
            $this->refreshTokens->add($refreshToken);
            $refreshToken->setUser($this);
        }

        return $this;
    }

    public function removeRefreshToken(RefreshToken $refreshToken): static
    {
        if ($this->refreshTokens->removeElement($refreshToken)) {
        }
    
        return $this;
    }
}
