<?php

namespace Tcieslar\EventProjection\Example\Event;

use Tcieslar\EventProjection\Example\Aggregate\CustomerId;
use Tcieslar\EventProjection\Example\Aggregate\OrderId;
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