<?php

namespace Tcieslar\EventProjection;

interface ProjectionStorageInterface
{
    public function get(string $viewClass, string $id): mixed;

    public function store(mixed $view, string $viewId): void;
}