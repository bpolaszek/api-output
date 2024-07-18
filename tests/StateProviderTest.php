<?php

namespace App\Tests;

it('creates a book', function () {
    $book = api()->post('/api/books', ['name' => 'PHP for dummies'])->toArray();

    expect($book['name'])->toBe('PHP for dummies') // <-- OK
        ->and($book['id'])->toBe(1) // <-- OK
        ->and($book['@id'])->toBe('/api/books/1') // <-- OK
        ->and($book['@type'])->toBe('Book') // <-- OK
        ->and($book['type'])->toBe('output'); // <-- Failed, got 'entity'
});

it('lists books', function () {
    $books = api()->get('/api/books')->toArray();
    $book = $books['hydra:member'][0];

    expect($book['name'])->toBe('PHP for dummies') // <-- OK
    ->and($book['id'])->toBe(1) // <-- OK
    ->and($book['type'])->toBe('output') // <-- OK
    ->and($book['@type'])->toBe('Book') // <-- Failed, got 'BookOutput'
    ->and($book['@id'])->toBe('/api/books/1'); // <-- Failed, got '/api/.well-known/genid/somerandomidentifier'
});
