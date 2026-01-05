<?php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\User\Input\RegisterUserInput;
use App\DTO\User\Output\RegisterUserOutput;
use App\Mapper\User\UserMapper;
use App\Service\User\UserRegistrationService;

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

