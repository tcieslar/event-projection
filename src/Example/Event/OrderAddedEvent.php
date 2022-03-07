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

    public function normalize(): array
    {
        return [
            'customer_id' => $this->customerId->toString(),
            'order_id' => $this->getOrderId()->toString(),
            'description' => $this->getOrderDescription(),
            'event_id' => $this->uuid->toString(),
            'occurred_at' => $this->occurredAt->format(DATE_RFC3339)
        ];
    }

    public static function denormalize(array $data): static
    {
        return new self(
            CustomerId::fromString($data['customer_id']),
            OrderId::fromString($data['order_id']),
            $data['description'],
            Uuid::fromString($data['event_id']),
            \DateTimeImmutable::createFromFormat(DATE_RFC3339, $data['occurred_at'])
        );
    }


}