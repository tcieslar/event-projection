<?php

namespace Tcieslar\EventProjection;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Elasticsearch\Common\Exceptions\ClientErrorResponseException;

class ElasticSearchProjectionStorage implements ProjectionStorageInterface
{
    private Serializer $serializer;
    private Client $client;
    /** @var ElasticSearchIndexSettingsProviderInterface[] */
    private array $settingsProviders;
    private LoggerInterface $logger;

    public function __construct(
        string          $host,
        string          $port,
        iterable        $settingsProviders,
        LoggerInterface $logger,
        ?Serializer     $serializer = null
    )
    {
        $this->settingsProviders = [];
        foreach ($settingsProviders as $settingsProvider) {
            $this->settingsProviders[] = $settingsProvider;
        }

        $this->client = ClientBuilder::create()
            ->setHosts([$host . ':' . $port])
            ->build();

        $this->logger = $logger;

        if ($serializer) {
            $this->serializer = $serializer;
            return;
        }
        $this->symfonySerializerFactory();
    }

    public function search(array $context): array
    {
        return [];
    }

    public function prepare(string $viewClass): void
    {
        $selectedProvider = null;
        foreach ($this->settingsProviders as $provider) {
            if ($provider->supportedView() === $viewClass) {
                $selectedProvider = $provider;
                break;
            }
        }

        if (!$selectedProvider) {
            throw  new \InvalidArgumentException('Settings provider not found.');
        }

        $indexName = $this->getIndexName($viewClass);
        $params = [
            'index' => $indexName,
            'body' => $selectedProvider->getSettings()
        ];

        $this->client->indices()->create($params);

        $this->logger->debug('Elastic Search - index settings for ' . $indexName);
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

        try {
            $response = $this->client->getSource($params);
        } catch (Missing404Exception $exception) {
            $this->logger->debug('Elastic Search - not found ' . $indexName . ', id ' . $id);
            return null;
        }

        $this->logger->debug('Elastic Search - get from ' . $indexName . ', id ' . $id);

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
        $this->client->index($params);

        $this->logger->debug('Elastic Search - store to ' . $indexName . ', id ' . $viewId);
    }

    public function getAll(string $viewClass, int $page = 1, int $pageLimit = 10): array
    {
        $indexName = $this->getIndexName($viewClass);
        $params = [
            'index' => $indexName,
            'body' => [
                'from' => ($page - 1) * $pageLimit,
                'size' => $pageLimit
            ]
        ];

        $start = round(microtime(true) * 1000);
        $response = $this->client->search($params);
        $stop = round(microtime(true) * 1000);
        $this->logger->debug('Elastic Search - search in ' . $indexName . ' (' . ($stop - $start) . ' ms).');
        $views = [];

        foreach ($response['hits']['hits'] as $item) {
            $views[] = $this->serializer->denormalize(
                $item['_source'],
                $viewClass
            );
        }

        return [
            'count' => $response['hits']['total']['value'],
            'views' => $views
        ];
    }

    public function deleteAll(string $viewClass): void
    {
        $indexName = $this->getIndexName($viewClass);
        $indexParams['index'] = $indexName;
        if (!$this->client->indices()->exists($indexParams)) {
            return;
        }

        $deleteParams = [
            'index' => $indexName
        ];
        $this->client->indices()->delete($deleteParams);

        $this->logger->debug('Elastic Search - delete all from ' . $indexName);
    }

    public function delete(string $viewClass, string $viewId): void
    {
        $indexName = $this->getIndexName($viewClass);
        $response = $this->client->delete([
            'index' => $indexName,
            'id' => $viewId
        ]);

        $this->logger->debug('Elastic Search - delete from ' . $indexName);
    }

    private function getIndexName(string $viewClass): string|array|null|false
    {
        $array = explode('\\', $viewClass);
        return mb_strtolower(array_pop($array));
    }
}