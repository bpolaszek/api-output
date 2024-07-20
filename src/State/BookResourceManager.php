<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Book;
use App\Entity\BookResource;
use App\Entity\BookResourceInput;

use function App\is_write_operation;
use function array_map;

/**
 * @implements ProviderInterface<BookResource>
 * @implements ProcessorInterface<BookResourceInput, BookResource>
 */
final readonly class BookResourceManager implements ProviderInterface, ProcessorInterface
{
    public function __construct(
        private CollectionProvider $collectionProvider,
        private ItemProvider $itemProvider,
        private Pagination $pagination,
        private PersistProcessor $persistProcessor,
        private RemoveProcessor $removeProcessor,
    ) {
    }

    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): ArrayPaginator|BookResource|null {
        return match ($operation instanceof CollectionOperationInterface) {
            true => $this->provideCollection($operation, $uriVariables, $context),
            false => $this->mapEntityToResource($this->itemProvider->provide($operation, $uriVariables, $context)),
        };
    }

    /**
     * @param BookResourceInput|BookResource $data
     */
    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): ?BookResource {
        if (is_write_operation($operation)) {
            return match ($operation instanceof Delete) {
                true => $this->handleDelete($data, $operation, $uriVariables, $context),
                false => $this->handleInsertOrUpdate($data, $operation, $uriVariables, $context),
            };
        }

        return $data;
    }

    private function provideCollection(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $items = $this->collectionProvider->provide($operation, $uriVariables, $context);

        return new ArrayPaginator(
            array_map([$this, 'mapEntityToResource'], [...$items]),
            $this->pagination->getOffset($operation, $context),
            $this->pagination->getLimit($operation, $context),
        );
    }

    private function handleInsertOrUpdate(
        BookResourceInput $input,
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): BookResource {
        if (($context['previous_data'] ?? null) instanceof BookResource) {
            $entity = $this->itemProvider->provide($operation, $uriVariables, $context);
        }
        $entity = $this->mapInputToEntity($input, $entity ?? null);
        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        return $this->mapEntityToResource($entity);
    }

    private function handleDelete(
        BookResource $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): null {
        $this->removeProcessor->process($data, $operation, $uriVariables, $context);

        return null;
    }

    private function mapInputToEntity(BookResourceInput $input, ?Book $book = null): Book
    {
        $book ??= new Book();
        $book->id = $input->id ?? $book->id;
        $book->author = $input->author ?? $book->author;
        $book->name = $input->name ?? $book->name;

        return $book;
    }

    private function mapEntityToResource(?Book $book): ?BookResource
    {
        if (null === $book) {
            return null;
        }

        $resource = new BookResource();
        $resource->id = $book->id;
        $resource->author = $book->author;
        $resource->name = $book->name;

        return $resource;
    }

}
