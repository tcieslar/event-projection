<?php

namespace Tcieslar\EventProjection;

interface EsIndexSettingsProviderInterface
{
    public function getSettings(): array;
}