<?php

namespace Tcieslar\EventProjection\Tests;

use PHPUnit\Framework\TestCase;
use Tcieslar\EventProjection\Example\Aggregate\CustomerId;
use Tcieslar\EventProjection\Example\Aggregate\OrderId;
use Tcieslar\EventProjection\Example\Event\CustomerCreatedEvent;
use Tcieslar\EventProjection\Example\Event\CustomerCredentialSetEvent;
use Tcieslar\EventProjection\Example\Event\OrderAddedEvent;
use Tcieslar\EventProjection\Example\Projection\Customer;
use Tcieslar\EventProjection\Example\Projection\CustomerProjection;
use Tcieslar\EventProjection\InMemoryProjectionStorage;
use Tcieslar\EventProjection\ProjectionManager;

class ProjectionManagerTest extends TestCase
{
    public function testProjectViews(): void
    {
        $projectionStorage = new InMemoryProjectionStorage();
        $projectionManager = new ProjectionManager(
            [
                new CustomerProjection($projectionStorage)
            ]
        );

        $customerId = CustomerId::create();
        $projectionManager->projectViews(
            new CustomerCreatedEvent(
                $customerId
            )
        );
        $projectionManager->projectViews(
            new CustomerCredentialSetEvent(
                $customerId,
                'test 2'
            )
        );

        $projectionManager->projectViews(
            new OrderAddedEvent(
                $customerId,
                OrderId::create(),
                'to jest zamównienie'
            )
        );

        $projectionManager->projectViews(
            new OrderAddedEvent(
                $customerId,
                OrderId::create(),
                'to jest zamównienie 2'
            )
        );

        $customer = $projectionStorage->get(Customer::class, $customerId->toString());

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals($customerId->toString(), $customer->getCustomerId()->toString());
        $this->assertEquals('test 2', $customer->getName());
        $this->assertEquals([
            'to jest zamównienie',
            'to jest zamównienie 2'
        ],
            $customer->getOrders());
    }
}