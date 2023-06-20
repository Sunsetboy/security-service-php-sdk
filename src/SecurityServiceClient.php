<?php

declare(strict_types=1);

namespace SecurityServiceClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use http\Client\Response;
use SecurityServiceClient\Dto\UserLoginDto;
use SecurityServiceClient\Exceptions\SecurityServiceException;

class SecurityServiceClient
{
    const ENV_PROD = 'prod';
    const ENV_TEST = 'test';

    private string $apiKey = '';
    private Client $httpClient;

    /**
     * @param Client $httpClient
     * @return SecurityServiceClient
     */
    public function setHttpClient(Client $httpClient): SecurityServiceClient
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    public function __construct(string $apiKey)
    {
        $this->httpClient = new Client([
           // Base URI is used with relative requests
           'base_uri' => $_ENV['SECURITY_SERVICE_URL'] ?? 'https://100yuristov.com/security/',
           // You can set any number of default request options.
           'timeout' => 2,
       ]);
        $this->apiKey = $apiKey;
    }

    /**
     * Logs user login and returns the suspicion score
     */
    public function logUserLogin(int $userId, string $ip, string $userAgent): int
    {
        $logResponse = $this->httpClient->request('POST', 'user-logins', [
            'json' => [
                "user_id" => $userId,
                "ip" => $ip,
                "login_ts" => (new \DateTime())->format('с'), // ISO 8601 date	2004-02-12T15:19:21+00:00
                "user_agent" => $userAgent,
            ],
            'headers' => [
                'X-API-KEY' => $this->apiKey,
            ]
        ]);

        $responseContent = $logResponse->getBody()->getContents();
        $responseParsed = json_decode($responseContent, true);

        if (!isset($responseParsed['data']['suspicion_score'])) {
            throw new SecurityServiceException('Incorrect security service response');
        }

        return (int)$responseParsed['data']['suspicion_score'];
    }

    /**
     * @param int[] $usersIds
     * @return UserLoginDto[]
     * @throws SecurityServiceException
     * @throws GuzzleException
     */
    public function fetchUsersLogins(array $usersIds): array
    {
        $logResponse = $this->httpClient->request('GET', 'user-logins', [
            'query' => [
                'user_id' => implode(',', $usersIds),
            ],
            'headers' => [
                'X-API-KEY' => $this->apiKey,
            ]
        ]);

        $responseContent = $logResponse->getBody()->getContents();

        return $this->createUserloginDtosFromJSON($responseContent);
    }

    /**
     * @param string $userLoginJSONResponse
     * @return UserLoginDto[]
     * @throws SecurityServiceException
     */
    private function createUserloginDtosFromJSON(string $userLoginJSONResponse): array
    {
        $responseParsed = json_decode($userLoginJSONResponse, true);
        if ($responseParsed['error'] == true) {
            $errorMessage = $responseParsed['message'] ?? "Unknown error";
            throw new SecurityServiceException($errorMessage);
        }
        if (empty($responseParsed['data'])) {
            return [];
        }
        $userLogins = [];
        foreach ($responseParsed['data'] as $item) {
            $userLogin = new UserLoginDto();
            $userLogin->setUserId($item['user_id']);
            $userLogin->setUserAgent($item['user_agent']);
            $userLogin->setIp($item['ip']);
            $userLogin->setTown($item['town']);
            $userLogin->setSuspicionScore($item['suspicion_score']);
            $userLogin->setLoginTs(new \DateTime($item['login_ts']));

            $userLogins[] = $userLogin;
        }

        return $userLogins;
    }
}
