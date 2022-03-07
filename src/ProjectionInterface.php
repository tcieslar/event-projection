<?php

namespace Tcieslar\EventProjection;

use Tcieslar\EventSourcing\Event;

interface ProjectionInterface
{
    public function handleEvent(Event $event): void;

    public function consumeEvent(Event $event): bool;
}