<?php

namespace Tcieslar\EventProjection\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Tcieslar\EventProjection\ElasticSearchProjectionStorage;
use PHPUnit\Framework\TestCase;
use Tcieslar\EventProjection\Example\Aggregate\CustomerId;
use Tcieslar\EventProjection\Example\Projection\Customer;

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
            new Serializer(
                $normalizers, $encoders
            )
        );

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
    }
}
