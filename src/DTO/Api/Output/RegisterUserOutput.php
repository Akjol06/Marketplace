<?php

namespace App\DTO\Api\Output;

use Symfony\Component\Serializer\Annotation\Groups;

class RegisterUserOutput
{
    #[Groups(['user:read'])]
    public int $id;

    #[Groups(['user:read'])]
    public string $email;

    #[Groups(['user:read'])]
    public ?string $phone = null;

    #[Groups(['user:read'])]
    public string $createdAt;
}

