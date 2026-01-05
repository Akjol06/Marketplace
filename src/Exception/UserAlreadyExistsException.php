<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserAlreadyExistsException extends ConflictHttpException
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('User with email "%s" already exists', $email));
    }
}

