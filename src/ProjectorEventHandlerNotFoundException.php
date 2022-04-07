<?php

namespace Tcieslar\EventProjection;

use Tcieslar\EventSourcing\Event;

class ProjectorEventHandlerNotFoundException extends \Exception
{
    public function __construct(string $viewClass, Event $event, \Throwable $previous)
    {
        parent::__construct(sprintf('Not handler found for view %s, event %s.', $viewClass, $event::class), 0, $previous);
    }
}