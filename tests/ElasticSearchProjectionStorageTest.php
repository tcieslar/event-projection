<?php

namespace Tcieslar\EventProjection\Tests;

use Psr\Log\NullLogger;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Tcieslar\EventProjection\ElasticSearchProjectionStorage;
use PHPUnit\Framework\TestCase;
use Tcieslar\EventProjection\Tests\Example\Aggregate\CustomerId;
use Tcieslar\EventProjection\Tests\Example\Projection\Customer;
use Tcieslar\EventProjection\Tests\Example\Query\CustomerQuery;

class ElasticSearchProjectionStorageTest extends TestCase
{
    public function testFirst(): void
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new DateTimeNormalizer(),
            new PropertyNormalizer(
                null, null, new ReflectionExtractor()
            )];

        $storage = new ElasticSearchProjectionStorage(
            'localhost',
            '9200',
            [
                new CustomerQuery()
            ],
            new NullLogger(),
            new Serializer(
                $normalizers, $encoders
            )
        );
        try {
            $storage->delete(Customer::class);
        }catch (\Throwable $exception) {

        }
        $storage->prepare(Customer::class);

        $customerId = CustomerId::create();
        $customer = new Customer(
            $customerId,
            \DateTimeImmutable::createFromFormat(DATE_RFC3339, (new \DateTimeImmutable())->format(DATE_RFC3339)),
            'test',
            ['test1', 'test2']
        );
        $storage->store(
            $customer,
            $customerId->toString()
        );

        $customer2 = $storage->get(Customer::class, $customerId->toString());
        $this->assertEquals($customer2, $customer);

        $result = $storage->getAll(Customer::class);
        $this->assertGreaterThan(0, $result['count']);
    }
}
