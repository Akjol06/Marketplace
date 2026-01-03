<?php

namespace App\State\Register;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Api\Output\Register\RegisterUserOutput;
use App\Mapper\Register\RegisterUserMapper;
use App\Service\Register\RegisterUserService;

class RegisterUserProcessor implements ProcessorInterface
{
    public function __construct(
        private RegisterUserService $registerService,
        private RegisterUserMapper $mapper
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): RegisterUserOutput
    {
        $user = $this->registerService->register($data);

        return $this->mapper->toOutput($user);
    }
}