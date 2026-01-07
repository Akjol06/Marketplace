<?php

namespace App\Tests\Api;

use App\Entity\RefreshToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthApiTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                'identifier' => 'user@example.com',
                'password' => 'password123',
            ])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('accessToken', $data);
        $this->assertArrayHasKey('refreshToken', $data);
        $this->assertEquals('user@example.com', $data['user']['email']);
    }

    public function testRefreshToken(): void
    {
        $client = static::createClient();

        $refreshToken = self::getContainer()
            ->get('doctrine')
            ->getRepository(RefreshToken::class)
            ->findOneBy(['revoked' => false]);

        $client->request(
            'POST',
            '/api/token/refresh',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                'refreshToken' => $refreshToken->getToken()
            ])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('accessToken', $data);
        $this->assertArrayHasKey('refreshToken', $data);
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get('doctrine')->getManager();
    
        $refreshToken = $em
            ->getRepository(RefreshToken::class)
            ->findOneBy(['revoked' => false]);
    
        $client->request(
            'POST',
            '/api/logout',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                'refreshToken' => $refreshToken->getToken()
            ])
        );
    
        $this->assertResponseIsSuccessful();
    
        $em->refresh($refreshToken);
    
        $this->assertTrue($refreshToken->isRevoked());
    }
}