<?php

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedInIntoBuilding;
use Building\Domain\DomainEvent\UserCheckedOutOfBuilding;
use DomainException;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $checkedInUsers = [];

    public static function new($name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        if (true === in_array($username, $this->checkedInUsers, true)) {
            throw new DomainException(sprintf('User "%s" is already checked in', $username));
        }

        $this->recordThat(UserCheckedInIntoBuilding::occur(
            $this->id(),
            [
                 'username' => $username,
            ]
        ));
    }

    public function checkOutUser(string $username)
    {
        if (false === in_array($username, $this->checkedInUsers)) {
            throw new DomainException(sprintf('User "%s" is not checked in', $username));
        }

        $this->recordThat(UserCheckedOutOfBuilding::occur(
            $this->id(),
            [
                'username' => $username,
            ]
        ));
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    public function whenUserCheckedInIntoBuilding(UserCheckedInIntoBuilding $event)
    {
        $username = $event->username();

        $this->checkedInUsers[] = $username;
        $this->checkedInUsers = array_unique($this->checkedInUsers);
    }

    public function whenUserCheckedOutOfBuilding(UserCheckedOutOfBuilding $event)
    {
        $username = $event->username();

        unset($this->checkedInUsers[array_search($username, $this->checkedInUsers)]);
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id() : string
    {
        return $this->aggregateId();
    }
}
