<?php

namespace Tcieslar\EventProjection\Tests\Example\Event;

use Tcieslar\EventProjection\Tests\Example\Aggregate\CustomerId;
use Tcieslar\EventProjection\Tests\Example\Event\DomainEventExample;
use Tcieslar\EventSourcing\Uuid;

class CustomerCredentialSetEvent extends DomainEventExample
{
    public function __construct(
        private CustomerId  $customerId,
        private string      $name,
        ?Uuid               $uuid = null,
        ?\DateTimeImmutable $occurredAt = null
    )
    {
        parent::__construct(
            $uuid,
            $occurredAt
        );
    }


    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}