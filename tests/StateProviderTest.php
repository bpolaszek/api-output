<?php

namespace App\Tests;

it('creates a book', function () {
    $book = api()->post('/api/books', ['name' => 'PHP for dummies'])->toArray();
    dump(['POST /api/books' => $book]);

    expect($book['name'])->toBe('PHP for dummies') // <-- OK
    ->and($book['id'])->toBe(1) // <-- OK
    ->and($book['@type'])->toBe('Book') // <-- OK
    ->and($book['@id'])->toBe('/api/books/1') // <-- OK
    ->and($book['type'])->toBe('output'); // <-- OK
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

it('handles 404s', function () {
    $response = api()->get('/api/books/2');
    dump(['GET /api/books/2' => $response->getStatusCode()]);

    expect($response->getStatusCode())->toBe(404); // <-- OK
});

it('updates a book', function () {
    $book = api()->put('/api/books/1', ['name' => 'The Easy PHP'])->toArray();
    dump(['PUT /api/books/1' => $book]);

    expect($book['name'])->toBe('The Easy PHP') // <-- OK
    ->and($book['id'])->toBe(1) // <-- OK
    ->and($book['@id'])->toBe('/api/books/1') // <-- OK
    ->and($book['@type'])->toBe('Book') // <-- OK
    ->and($book['type'])->toBe('output'); // <-- OK
});

it('deletes a book', function () {
    $content = api()->delete('/api/books/1')->getContent();
    dump(['DELETE /api/books/1' => $content]);

    expect($content)->toBeEmpty(); // <-- OK
});
