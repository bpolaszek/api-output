<?php

namespace App\Tests;

it('creates a book', function () {
    $book = api()->post('/api/books', ['name' => 'PHP for dummies'])->toArray();
    dump(['POST /api/books' => $book]);

    expect($book['name'])->toBe('PHP for dummies') // <-- OK
    ->and($book['id'])->toBe(1) // <-- OK
    ->and($book['@type'])->toBe('Book') // <-- OK
    ->and($book['@id'])->toBe('/api/books/1') // <-- Failed, got '/api/.well-known/genid/somerandomidentifier'
    ->and($book['type'])->toBe('output'); // <-- Failed, got 'entity'
});

it('lists books', function () {
    $books = api()->get('/api/books')->toArray();
    dump(['GET /api/books' => $books]);

    $book = $books['hydra:member'][0];

    expect($book['name'])->toBe('PHP for dummies') // <-- OK
    ->and($book['id'])->toBe(1) // <-- OK
    ->and($book['type'])->toBe('output') // <-- OK
    ->and($book['@type'])->toBe('Book') // OK
    ->and($book['@id'])->toBe('/api/books/1'); // OK
});


it('gets a book', function () {
    $book = api()->get('/api/books/1')->toArray();
    dump(['GET /api/books/1' => $book]);

    expect($book['name'])->toBe('PHP for dummies') // <-- OK
    ->and($book['id'])->toBe(1) // <-- OK
    ->and($book['@id'])->toBe('/api/books/1') // <-- OK
    ->and($book['@type'])->toBe('Book') // <-- OK
    ->and($book['type'])->toBe('output'); // <-- OK
});
