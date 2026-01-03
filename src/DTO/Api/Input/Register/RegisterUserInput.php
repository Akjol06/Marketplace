<?php

namespace App\DTO\Api\Input\Register;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class RegisterUserInput
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:read', 'user:write'])]
    public string $email;

    #[Assert\Length(min: 20)]
    #[Groups(['user:read', 'user:write'])]
    public ?string $phone = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    #[Groups(['user:write'])]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    #[Groups(['user:write'])]
    public string $passwordConfirmation;

    #[Assert\Callback]
    public function validatePasswords(): void
    {
        if ($this->password !== $this->passwordConfirmation) {
            throw new ConflictHttpException('User already exists');
        }
    }
}