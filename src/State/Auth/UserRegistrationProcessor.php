<?php

namespace App\State\Auth;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Api\Input\RegisterUserInput;
use App\DTO\Api\Output\RegisterUserOutput;
use App\Mapper\User\UserMapper;
use App\Service\Auth\UserRegistrationService;

class UserRegistrationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserRegistrationService $registrationService,
        private readonly UserMapper $userMapper
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): RegisterUserOutput
    {
        if (!$data instanceof RegisterUserInput) {
            throw new \InvalidArgumentException('Expected RegisterUserInput instance');
        }

        $user = $this->registrationService->register($data);

        return $this->userMapper->toOutput($user);
    }
}

