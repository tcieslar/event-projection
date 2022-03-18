<?php

namespace Tcieslar\EventProjection;

interface ElasticSearchIndexSettingsProviderInterface
{
    public function supportedView(): string;
    public function getSettings(): array;
}