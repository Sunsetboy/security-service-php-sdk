<?php
declare(strict_types=1);

namespace SecurityServiceClient\Dto;

class UserLoginDto
{
    private int $userId = 0;
    private string $ip = '';
    private string $userAgent = '';
    private int $suspicionScore = 0;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return UserLoginDto
     */
    public function setUserId(int $userId): UserLoginDto
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return UserLoginDto
     */
    public function setIp(string $ip): UserLoginDto
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return UserLoginDto
     */
    public function setUserAgent(string $userAgent): UserLoginDto
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return int
     */
    public function getSuspicionScore(): int
    {
        return $this->suspicionScore;
    }

    /**
     * @param int $suspicionScore
     * @return UserLoginDto
     */
    public function setSuspicionScore(int $suspicionScore): UserLoginDto
    {
        $this->suspicionScore = $suspicionScore;
        return $this;
    }
}
