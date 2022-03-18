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

    public function getAll(string $viewClass, int $page = 1, int $pageLimit = 10): array
    {
        return array_slice($this->views, ($page - 1) * $pageLimit, $pageLimit);
    }

    public function delete(string $viewClass): void
    {
        if(isset($this->views[$viewClass])) {
            unset($this->views[$viewClass]);
        }
    }

    public function prepare(string $viewClass): void
    {
        $this->views[$viewClass] = [];
    }
}