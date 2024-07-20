<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use App\State\BookResourceManager;

#[ApiResource(
    shortName: 'Book',
    input: BookResourceInput::class,
    provider: BookResourceManager::class,
    processor: BookResourceManager::class,
    stateOptions: new Options(Book::class),
)]
final class BookResource
{
    public int $id;
    public string $author;
    public string $name;
    public string $type = 'output';
}
