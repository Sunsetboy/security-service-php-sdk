<?php
declare(strict_types=1);

namespace SecurityServiceClient;

use GuzzleHttp\Client;
use SecurityServiceClient\Dto\UserLoginDto;

class SecurityServiceClient
{
    private string $apiKey = '';

    public function __construct(string $apiKey)
    {
        $this->httpClient = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://100yuristov.com/security/',
            // You can set any number of default request options.
            'timeout' => 2,
        ]);
        $this->apiKey = $apiKey;
    }

    /**
     * Logs user login and returns the suspicion score
     */
    public function logUserLogin(int $userId, string $ip, string $userAgent): UserLoginDto
    {

    }

    /**
     * @param int[] $usersIds
     * @return UserLoginDto[]
     */
    public function fetchUsersLogins(array $usersIds): array
    {

    }
}
