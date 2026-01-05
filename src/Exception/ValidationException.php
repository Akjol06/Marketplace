<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationException extends BadRequestHttpException
{
    public function __construct(string $message = 'Validation failed')
    {
        parent::__construct($message);
    }
}

