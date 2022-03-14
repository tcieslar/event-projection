<?php

namespace Tcieslar\EventProjection\Tests\Example\Projection;

use DateTimeImmutable;
use Tcieslar\EventProjection\Tests\Example\Aggregate\CustomerId;

class Customer
{
    public function __construct(
        private CustomerId         $customerId,
        private ?DateTimeImmutable $createdAt = null,
        private ?string            $name = null,
        private ?array             $orders = null,

    )
    {
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    public function setCustomerId(CustomerId $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOrders(): ?array
    {
        return $this->orders;
    }

    public function setOrders(?array $orders): void
    {
        $this->orders = $orders;
    }
}