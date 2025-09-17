<?php

namespace App\Model\Services;

use App\Model\Entities\Book;
use Elasticsearch\Client;

class BookSearchService
{
    public function __construct(
        private Client $client,
    ) {}

    public function index(Book $book): void
    {
        $this->client->index([
            'index' => 'books',
            'id' => $book->getId(),
            'body' => [
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'year' => $book->getYear(),
                'isbn' => $book->getIsbn(),
            ],
        ]);
    }

    public function delete(int $bookId): void
    {
        $this->client->delete([
            'index' => 'books',
            'id' => $bookId,
        ]);
    }

    public function search(string $query, int $from = 0, int $size = 10): array
    {
        $response = $this->client->search([
            'index' => 'books',
            'from' => $from,
            'size' => $size,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title^2', 'author', 'isbn'],
                    ],
                ],
            ],
        ]);

        return array_map(
            fn(array $hit) => (int) $hit['_id'],
            $response['hits']['hits']
        );
    }

    public function countSearchResults(string $query): int
    {
        $response = $this->client->search([
            'index' => 'books',
            'size' => 0,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title^2', 'author', 'isbn'],
                    ],
                ],
            ],
        ]);

        return $response['count'] ?? 0;
    }
}