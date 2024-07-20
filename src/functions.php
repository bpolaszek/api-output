<?php

namespace App;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

function is_write_operation(Operation $operation, bool $includeDelete = true): bool
{
    $isCreateOperation = $operation instanceof Post;
    $isUpdateOperation = $operation instanceof Put || $operation instanceof Patch;
    $isDeleteOperation = $operation instanceof Delete;

    return match ($includeDelete) {
        true => $isCreateOperation || $isUpdateOperation || $isDeleteOperation,
        false => $isCreateOperation || $isUpdateOperation,
    };
}
