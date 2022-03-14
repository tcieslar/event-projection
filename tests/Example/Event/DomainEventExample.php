<?php declare(strict_types=1);

namespace Tcieslar\EventProjection\Tests\Example\Event;

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
        $dateTime = DateTimeImmutable::createFromFormat(
            DATE_RFC3339,
            (new \DateTimeImmutable())->format(DATE_RFC3339)
        );
        if (false === $dateTime) {
            throw new \RuntimeException('Time generation error.');
        }
        $this->occurredAt = $occurredAt ?? $dateTime;
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