<?php

namespace App\State\Auth;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Api\Output\RefreshTokenOutput;
use App\Service\Auth\RefreshTokenService;

class RefreshTokenProcessor implements ProcessorInterface
{
    public function __construct(
        private RefreshTokenService $service,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): RefreshTokenOutput
    {
        /** @var RefreshTokenInput $data */
        return $this->service->refresh($data->refreshToken);
    }
}