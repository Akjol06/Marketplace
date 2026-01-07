<?php

namespace App\Tests\Api;

use App\Entity\RefreshToken;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class AuthApiTest extends WebTestCase
{
    private $client;
    private $em;
    private $hasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();

        $this->em = $container->get('doctrine')->getManager();
        $this->hasher = $container->get('security.password_hasher');
    }

    private function createUser(?string $email = null, string $password = 'password123'): User
    {
        $email = $email ?? 'user+' . uniqid() . '@example.com';

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function createRefreshToken(User $user): RefreshToken
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setToken(Uuid::v4()->toRfc4122());
        $refreshToken->setUser($user);
        $refreshToken->setExpiresAt(new \DateTimeImmutable('+30 days'));
        $refreshToken->setRevoked(false);

        $this->em->persist($refreshToken);
        $this->em->flush();

        return $refreshToken;
    }

    public function testLoginSuccess(): void
    {
        $user = $this->createUser();

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                'identifier' => $user->getEmail(),
                'password' => 'password123',
            ])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('accessToken', $data);
        $this->assertArrayHasKey('refreshToken', $data);
        $this->assertEquals($user->getEmail(), $data['user']['email']);
    }

    public function testLoginFailure(): void
    {
        $user = $this->createUser();

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                'identifier' => $user->getEmail(),
                'password' => 'wrong-password',
            ])
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testRefreshTokenRotation(): void
    {
        $user = $this->createUser();
        $refreshToken = $this->createRefreshToken($user);
        $oldToken = $refreshToken->getToken();

        $this->client->request(
            'POST',
            '/api/token/refresh',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode(['refreshToken' => $oldToken])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('accessToken', $data);
        $this->assertArrayHasKey('refreshToken', $data);
        $this->assertNotEquals($oldToken, $data['refreshToken']);

        $this->em->refresh($refreshToken);
        $this->assertTrue($refreshToken->isRevoked());
    }

    public function testLogout(): void
    {
        $user = $this->createUser('user+' . uniqid() . '@example.com');
        $refreshToken = $this->createRefreshToken($user);
    
        $client = $this->client;
    
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
                'identifier' => $user->getEmail(),
                'password' => 'password123',
            ])
        );
    
        $data = json_decode($client->getResponse()->getContent(), true);
        $accessToken = $data['accessToken'];
    
        $client->request(
            'POST',
            '/api/logout',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $accessToken,
            ],
            json_encode([
                'refreshToken' => $refreshToken->getToken()
            ])
        );
    
        $this->assertResponseIsSuccessful();
    
        $tokenFromDb = $this->em->getRepository(RefreshToken::class)
            ->find($refreshToken->getId());
    
        $this->assertNotNull($tokenFromDb);
        $this->assertTrue($tokenFromDb->isRevoked());
    }
}