<?php declare(strict_types=1);

namespace Tcieslar\EventProjection\Example\Event;

use DateTimeImmutable;
use Tcieslar\EventSourcing\Event;
use Tcieslar\EventSourcing\Uuid;

abstract class DomainEventExample implements Event
{
    protected Uuid $uuid;
    protected DateTimeImmutable $occurredAt;

    public function __construct(
        ?Uuid              $uuid,
        ?DateTimeImmutable $occurredAt
    )
    {
        $this->uuid = $uuid ?? Uuid::random();
        $this->occurredAt = $occurredAt ?? DateTimeImmutable::createFromFormat(
                DATE_RFC3339,
                (new \DateTimeImmutable())->format(DATE_RFC3339)
            );
    }

    public function getEventId(): Uuid
    {
        return $this->uuid;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}