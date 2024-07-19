<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use App\State\BookProvider;

#[ApiResource(
    shortName: 'Book',
    input: Book::class,
    output: BookOutput::class,
    provider: BookProvider::class,
    stateOptions: new Options(Book::class),
)]
final class BookOutput
{
    public int $id;
    public string $name;
    public string $type = 'output';
}
