<?php

namespace App;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use InvalidArgumentException;

use function in_array;

function is_write_operation(Operation $operation, bool $includeDelete = true): bool
{
    if (!$operation instanceof HttpOperation) {
        throw new InvalidArgumentException('This function only supports HTTP operations.');
    }

    $method = $operation->getMethod();
    $isCreateOperation = 'POST' === $method;
    $isUpdateOperation = in_array($method, ['PUT', 'PATCH'], true);
    $isDeleteOperation = 'DELETE' === $method;

    return match ($includeDelete) {
        true => $isCreateOperation || $isUpdateOperation || $isDeleteOperation,
        false => $isCreateOperation || $isUpdateOperation,
    };
}
