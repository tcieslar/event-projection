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
    public function __construct(iterable $projections)
    {
        $this->projections = [];
        foreach ($projections as $projection) {
            $this->projections[] = $projection;
        }
    }

    public function getProjecitonViewClasses(): array
    {
        $classes = [];
        foreach ($this->projections as $projection) {
            $classes[] = $projection->processedView();
        }

        return $classes;
    }

    public function projectViewsByEventCollection(EventCollection $eventCollection, ?string $selectedViewClass = null): void
    {
        foreach ($eventCollection as $event) {
            $this->projectViews($event, $selectedViewClass);
        }
    }

    public function projectViews(Event $event, ?string $selectedViewClass = null): void
    {
        foreach ($this->projections as $projection) {
            if ($selectedViewClass &&
                $projection->processedView() !== $selectedViewClass) {
                continue;
            }

            if (!$projection->supportsEvent($event)) {
                continue;
            }
            $projection->handleEvent($event);
        }
    }
}