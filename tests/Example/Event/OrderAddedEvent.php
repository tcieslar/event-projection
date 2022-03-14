<?php

namespace Tcieslar\EventProjection\Tests\Example\Event;

use Tcieslar\EventProjection\Tests\Example\Aggregate\CustomerId;
use Tcieslar\EventProjection\Tests\Example\Aggregate\OrderId;
use Tcieslar\EventProjection\Tests\Example\Event\DomainEventExample;
use Tcieslar\EventSourcing\Uuid;

class OrderAddedEvent extends DomainEventExample
{
    public function __construct(
        private CustomerId  $customerId,
        private OrderId     $orderId,
        private string      $orderDescription,
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

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getOrderDescription(): string
    {
        return $this->orderDescription;
    }
}