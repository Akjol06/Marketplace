<?php

namespace App\State\Auth;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\Auth\RefreshTokenService;

class LogoutProcessor implements ProcessorInterface
{
    public function __construct(private RefreshTokenService $service) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        /** @var LogoutInput $data */
        $refresh = $this->service->validate($data->refreshToken);
        $this->service->revoke($refresh);

        return ['success' => true];
    }
}