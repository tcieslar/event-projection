<?php

namespace Tcieslar\EventProjection;

interface ProjectionStorageInterface
{
    public function prepare(string $viewClass): void;

    public function deleteAll(string $viewClass): void;

    public function getAll(string $viewClass, int $page = 1, int $pageLimit = 10): array;

    public function get(string $viewClass, string $viewId): mixed;

    public function store(mixed $view, string $viewId): void;

    public function delete(string $viewClass, string $viewId): void;
}