<?php

namespace App\DTO\Api\Input;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserInput
{
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Email is invalid')]
    #[Groups(['user:read', 'user:write'])]
    public string $email;

    #[Assert\Length(min: 10, max: 20, minMessage: 'Phone must be at least {{ limit }} characters', maxMessage: 'Phone must not exceed {{ limit }} characters')]
    #[Groups(['user:read', 'user:write'])]
    public ?string $phone = null;

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters')]
    #[Groups(['user:write'])]
    public string $password;

    #[Assert\NotBlank(message: 'Password confirmation is required')]
    #[Assert\EqualTo(propertyPath: 'password', message: 'Passwords do not match')]
    #[Groups(['user:write'])]
    public string $passwordConfirmation;
}

