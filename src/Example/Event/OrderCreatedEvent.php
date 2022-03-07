<?php declare(strict_types=1);

namespace Tcieslar\EventProjection\Example\Event;

use Tcieslar\EventProjection\Example\Aggregate\OrderId;
use Tcieslar\EventSourcing\Uuid;

class OrderCreatedEvent extends DomainEventExample
{
    public function __construct(
        private OrderId     $orderId,
        private string      $description,
        ?Uuid               $uuid = null,
        ?\DateTimeImmutable $occurredAt = null
    )
    {
        parent::__construct(
            $uuid,
            $occurredAt
        );
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}