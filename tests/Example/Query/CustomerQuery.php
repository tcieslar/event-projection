<?php

namespace Tcieslar\EventProjection\Tests\Example\Query;

use Tcieslar\EventProjection\ElasticSearchIndexSettingsProviderInterface;
use Tcieslar\EventProjection\Tests\Example\Projection\Customer;

class CustomerQuery implements ElasticSearchIndexSettingsProviderInterface
{
    public function supportedView(): string
    {
        return Customer::class;
    }

    public function getSettings(): array
    {
        return [
            "mappings" => [
                "properties" => [
                    "createdAt" => [
                        "type" => "date",
                        "format" => "yyyy-MM-dd'T'HH:mm:ssZZZZZ"
                    ]
                ]
            ]
        ];
    }
}