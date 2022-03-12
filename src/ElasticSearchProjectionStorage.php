<?php

namespace Tcieslar\EventProjection;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class ElasticSearchProjectionStorage implements ProjectionStorageInterface
{
    private Serializer $serializer;
    private Client $client;

    public function __construct(string $host, string $port)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new DateTimeNormalizer(),
            new PropertyNormalizer(
                null, null, new ReflectionExtractor()
            )];
        $this->serializer = new Serializer(
            $normalizers, $encoders
        );

        $this->client = ClientBuilder::create()
            ->setHosts([$host . ':' . $port])
            ->build();
    }

    public function get(string $viewClass, string $id): mixed
    {
        $array = explode('\\', $viewClass);
        $indexName = mb_strtolower($array[count($array) - 1]);
        $params = [
            'index' => $indexName,
            'id' => $id
        ];

        $response = $this->client->getSource($params);
        return $this->serializer->denormalize(
            $response,
            $viewClass
        );
    }

    public function store(mixed $view, string $viewId): void
    {
        $array = explode('\\', get_class($view));
        $indexName = mb_strtolower($array[count($array) - 1]);
        $serialized = $this->serializer->serialize($view, 'json');

        $params = [
            'index' => $indexName,
            'id' => $viewId,
            'body' => $serialized
        ];
        $response = $this->client->index($params);
    }
}