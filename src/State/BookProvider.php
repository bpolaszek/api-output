<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Book;
use App\Entity\BookOutput;

use function array_map;

/**
 * @implements ProviderInterface<BookOutput>
 */
final readonly class BookProvider implements ProviderInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
        private ItemProvider $itemProvider,
        private Pagination $pagination,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ArrayPaginator|BookOutput
    {
        return match ($operation instanceof CollectionOperationInterface) {
            true => $this->provideCollection($operation, $uriVariables, $context),
            false => $this->provideItem($operation, $uriVariables, $context),
        };
    }

    public function provideCollection(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $items = $this->collectionProvider->provide($operation, $uriVariables, $context);

        return new ArrayPaginator(
            array_map([$this, 'transformItem'], [...$items]),
            $this->pagination->getOffset($operation, $context),
            $this->pagination->getLimit($operation, $context),
        );
    }

    private function provideItem(Operation $operation, array $uriVariables = [], array $context = []): BookOutput
    {
        return $this->transformItem($this->itemProvider->provide($operation, $uriVariables, $context));
    }

    private function transformItem(Book $entity): BookOutput
    {
        $output = new BookOutput();
        $output->id = $entity->id;
        $output->name = $entity->name;

        return $output;
    }

}
