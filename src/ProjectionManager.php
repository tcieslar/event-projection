<?php

namespace Tcieslar\EventProjection;

use Tcieslar\EventSourcing\Event;
use Tcieslar\EventSourcing\EventCollection;

class ProjectionManager
{
    /**
     * @var array<ProjectionInterface>
     */
    private array $projections;

    /**
     * @param array<ProjectionInterface> $projections
     */
    public function __construct(array $projections)
    {
        $this->projections = $projections;
    }

    public function projectViewsByEventCollection(EventCollection $eventCollection): void
    {
        foreach ($eventCollection  as $event) {
            $this->projectViews($event);
        }
    }

    public function projectViews(Event $event): void
    {
        foreach ($this->projections as $projection) {
            if (!$projection->consumeEvent($event)) {
                continue;
            }
            $projection->handleEvent($event);
        }
    }
}