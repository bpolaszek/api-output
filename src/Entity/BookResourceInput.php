<?php

namespace App\Entity;

final class BookResourceInput
{
    public ?int $id = null;
    public ?string $author = null;
    public string $name;
    public string $type = 'input';
}
