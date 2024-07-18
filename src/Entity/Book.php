<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\State\BookProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    output: BookOutput::class,
    provider: BookProvider::class,
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column]
    public string $name;

    public string $type = 'entity';
}
