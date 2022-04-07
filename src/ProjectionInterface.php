<?php

namespace Tcieslar\EventProjection;

use Tcieslar\EventSourcing\Event;

interface ProjectionInterface
{
    public function supportsEvent(Event $event): bool;

    public function processedView(): string;
}