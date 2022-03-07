<?php

namespace Tcieslar\EventProjection\Example\Event;

use Tcieslar\EventProjection\Example\Aggregate\CustomerId;
use Tcieslar\EventSourcing\Uuid;

class CustomerCreatedEvent extends DomainEventExample
{
    public function __construct(
        private CustomerId  $customerId,
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
}