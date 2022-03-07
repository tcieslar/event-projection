<?php

namespace Tcieslar\EventProjection\Example\Projection;

use DateTimeImmutable;
use Tcieslar\EventProjection\Example\Aggregate\CustomerId;

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
}