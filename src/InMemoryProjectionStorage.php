<?php

namespace Tcieslar\EventProjection;

class InMemoryProjectionStorage implements ProjectionStorageInterface
{
    private array $views = [];

    public function get(string $viewClass, string $id): mixed
    {
        return $this->views[$viewClass][$id] ?? null;
    }

    public function store(mixed $view, string $viewId): void
    {
        $this->views[get_class($view)] ??= [];
        $this->views[get_class($view)][$viewId] = $view;
    }
}