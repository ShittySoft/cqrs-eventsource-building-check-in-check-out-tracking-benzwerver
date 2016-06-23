<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;

final class CheckUserInIntoBuilding extends Command
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $buildingId;

    /**
     * @param string $username
     * @param string $buildingId
     */
    public function __construct(string $username, string $buildingId)
    {
        $this->init();

        $this->username = $username;
        $this->buildingId = $buildingId;
    }

    /**
     * @param string $username
     * @param string $buildingId
     *
     * @return CheckUserInIntoBuilding
     */
    public static function fromUsernameAndBuilding(string $username, string $buildingId): self
    {
        return new self($username, $buildingId);
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getBuildingId(): string
    {
        return $this->buildingId;
    }

    /**
     * {@inheritdoc}
     */
    public function payload()
    {
        return [
            'username' => $this->username,
            'buildingId' => $this->buildingId,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setPayload(array $payload)
    {
        $this->username = $payload['username'];
        $this->buildingId = $payload['buildingId'];
    }
}
