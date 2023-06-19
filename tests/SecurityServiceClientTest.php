<?php
declare(strict_types=1);

namespace tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SecurityServiceClient\Dto\UserLoginDto;
use SecurityServiceClient\SecurityServiceClient;

class SecurityServiceClientTest extends TestCase
{

    public function testSendLogin(): void
    {
        $mock = new MockHandler([
            new Response(201, ['Content-Type' => 'application/json'], json_encode([
                "error" => false,
                "message" => "Login saved",
                "data" => [
                    "suspicion_score" => 30,
                ]
            ])),
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), new Response(400))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $securityServiceClient = new SecurityServiceClient('api-key');
        $securityServiceClient->setHttpClient($client);

        $score = $securityServiceClient->logUserLogin(42, '1.1.1.1', 'Mozilla 123');
        $this->assertEquals(30, $score);

        $this->expectException(ClientException::class);
        $score = $securityServiceClient->logUserLogin(42, '1.1.1.1', 'Mozilla 123');
    }

    public function testGetLogins()
    {
        $mock = new MockHandler([
            new Response(201, ['Content-Type' => 'application/json'], json_encode([
                "error" => false,
                "message" => "Last logins",
                "data" => [
                    [
                        "Id" => 16,
                        "company_id" => 1,
                        "user_id" => 1,
                        "login_ts" => "2023-06-01T10:22:45Z",
                        "ip" => "2.2.2.2",
                        "user_agent" => "Opera",
                        "town" => "Коряжма",
                        "suspicion_score" => 10,
                    ],
                    [
                        "Id" => 20,
                        "company_id" => 1,
                        "user_id" => 2,
                        "login_ts" => "2023-06-01T10:22:45Z",
                        "ip" => "2.2.2.3",
                        "user_agent" => "Opera",
                        "town" => "Коряжма",
                        "suspicion_score" => 30,
                    ],
                ]
            ])),
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), new Response(400))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $securityServiceClient = new SecurityServiceClient('api-key');
        $securityServiceClient->setHttpClient($client);

        $logins = $securityServiceClient->fetchUsersLogins([1, 2]);
        $this->assertCount(2, $logins);
        $this->assertInstanceOf(UserLoginDto::class, $logins[0]);
        $this->assertEquals('2.2.2.2', $logins[0]->getIp());

        $this->expectException(ClientException::class);
        $loginsWithError = $securityServiceClient->fetchUsersLogins([1, 2]);
    }
}