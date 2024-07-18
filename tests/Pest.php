<?php

namespace App\Tests;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Ulid;

uses()->beforeAll(fn () => resetDatabase())->in(__DIR__);

function app(): KernelInterface
{
    static $kernel;

    return $kernel ??= (function () {
        $testCase = new class () extends KernelTestCase {
            public function __construct()
            {
                parent::__construct((string) new Ulid());
            }

            public function getKernel(): KernelInterface
            {
                self::bootKernel();

                return self::$kernel;
            }
        };

        return $testCase->getKernel();
    })();
}

function container(): ContainerInterface
{
    return app()->getContainer()->get('test.service_container');
}

function api(): ApiClient
{
    static $api;

    return $api ??= new ApiClient(container()->get('test.api_platform.client'));
}

function resetDatabase(): void
{
    /** @var EntityManagerInterface $em */
    $em = container()->get(EntityManagerInterface::class);
    $schemaTool = new SchemaTool($em);
    $schemaTool->dropDatabase();
    $schemaTool->createSchema([$em->getClassMetadata(Book::class)]);
}
