<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Book;
use App\Entity\BookOutput;

use function App\is_write_operation;

/**
 * @implements ProcessorInterface<Book, BookOutput>
 */
final readonly class BookProcessor implements ProcessorInterface
{
    public function __construct(
        private PersistProcessor $persistProcessor,
        private RemoveProcessor $removeProcessor,
        private BookProvider $bookProvider,
    ) {
    }

    /**
     * @param Book|BookOutput $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BookOutput
    {
        if (is_write_operation($operation)) {
            return match ($operation instanceof Delete) {
                true => $this->handleDelete($data, $operation, $uriVariables, $context),
                false => $this->handleInsertOrUpdate($data, $operation, $uriVariables, $context),
            };
        }

        return $data;
    }

    private function handleInsertOrUpdate(Book $data, Operation $operation, array $uriVariables = [], array $context = []): BookOutput
    {
        if ($operation instanceof Put) {
            $data->id = $data->id ?? $context['previous_data']->id;
            $data->name = $data->name ?? $context['previous_data']->name;
        }

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        return $this->bookProvider->transformItem($data);
    }

    private function handleDelete(BookOutput $data, Operation $operation, array $uriVariables = [], array $context = []): BookOutput
    {
        $this->removeProcessor->process($data, $operation, $uriVariables, $context);

        return $data;
    }
}
