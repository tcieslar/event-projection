<?php

namespace Tcieslar\EventProjection\Example\Event;

use Tcieslar\EventProjection\Example\Aggregate\CustomerId;
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