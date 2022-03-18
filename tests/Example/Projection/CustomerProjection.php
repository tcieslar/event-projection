<?php

namespace Tcieslar\EventProjection\Tests\Example\Projection;

use Tcieslar\EventProjection\Tests\Example\Event\CustomerCreatedEvent;
use Tcieslar\EventProjection\Tests\Example\Event\CustomerCredentialSetEvent;
use Tcieslar\EventProjection\Tests\Example\Event\OrderAddedEvent;
use Tcieslar\EventProjection\ProjectionInterface;
use Tcieslar\EventProjection\ProjectionStorageInterface;
use Tcieslar\EventSourcing\Event;

class CustomerProjection implements ProjectionInterface
{
    private ProjectionStorageInterface $projectionStorage;

    public function __construct(ProjectionStorageInterface $projectionStorage)
    {
        $this->projectionStorage = $projectionStorage;
    }

    public function handleEvent(Event $event): void
    {
        if ($event instanceof CustomerCreatedEvent) {
            $customer = new Customer($event->getCustomerId(), $event->getOccurredAt());
            $this->projectionStorage->store($customer, $event->getCustomerId()->toString());
        }

        if ($event instanceof CustomerCredentialSetEvent) {
            /** @var Customer $customer */
            $customer = $this->projectionStorage->get(Customer::class, $event->getCustomerId()->toString());
            $customer->setName($event->getName());
            $this->projectionStorage->store($customer, $event->getCustomerId()->toString());
        }

        if ($event instanceof OrderAddedEvent) {
            $customer = $this->projectionStorage->get(Customer::class, $event->getCustomerId()->toString());
            $orders = $customer->getOrders();
            $orders[] = $event->getOrderDescription();
            $customer->setOrders($orders);
            $this->projectionStorage->store($customer, $event->getCustomerId()->toString());
        }
    }

    public function supportsEvent(Event $event): bool
    {
        return in_array(get_class($event),
            [
                CustomerCreatedEvent::class,
                CustomerCredentialSetEvent::class,
                OrderAddedEvent::class
            ]);
    }

    public function processedView(): string
    {
        return Customer::class;
    }
}