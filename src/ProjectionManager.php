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

    public function initializeProjection(string $viewClass): void
    {
//        $this->projectionStorage->delete($viewClass);
//        $this->projectionStorage->prepare($viewClass);
    }

    public function getProjecitonViewClasses(): array
    {
        $classes = [];
        foreach ($this->projections as $projection) {
            $classes[] = $projection->processedView();
        }

        return $classes;
    }

    // selectedViewClass only in projectView
    public function projectViewsByEventCollection(EventCollection $eventCollection): void
    {
        foreach ($eventCollection as $event) {
            $this->projectViews($event);
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