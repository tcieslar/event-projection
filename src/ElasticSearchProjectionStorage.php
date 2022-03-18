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

    public function __construct(string $host, string $port, ?Serializer $serializer = null)
    {
        $this->client = ClientBuilder::create()
            ->setHosts([$host . ':' . $port])
            ->build();

        if ($serializer) {
            $this->serializer = $serializer;
            return;
        }
        $this->symfonySerializerFactory();
    }

    private function symfonySerializerFactory(): void
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
    }

    public function get(string $viewClass, string $id): mixed
    {
        $indexName = $this->getIndexName($viewClass);
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
        $indexName = $this->getIndexName(get_class($view));
        $serialized = $this->serializer->serialize($view, 'json');

        $params = [
            'index' => $indexName,
            'id' => $viewId,
            'body' => $serialized
        ];
        $response = $this->client->index($params);
    }

    public function getAll(string $viewClass, int $page = 1, int $pageLimit = 10): array
    {
        $params = [
            'index' => $this->getIndexName($viewClass),
            'body' => [
                'from' => ($page - 1) * $pageLimit,
                'size' => $pageLimit
            ]
        ];

        $response = $this->client->search($params);
        $views = [];

        foreach ($response['hits']['hits'] as $item) {
            $views[] = $this->serializer->denormalize(
                $item['_source'],
                $viewClass
            );
        }

        return [
            'count' => $response['hits']['total'],
            'views' => $views
        ];
    }

    public function delete(string $viewClass): void
    {
        $deleteParams = [
            'index' => $this->getIndexName($viewClass)
        ];
        $response = $this->client->indices()->delete($deleteParams);
    }

    private function getIndexName(string $viewClass): string|array|null|false
    {
        $array = explode('\\', $viewClass);
        return mb_strtolower(array_pop($array));
    }
}