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

    public function searchWithFilters(array $params, int $from = 0, int $size = 10): array
    {
        $must = [];

        if (!empty($params['query'])) {
            $must[] = [
                'multi_match' => [
                    'query' => $params['query'],
                    'fields' => ['title^2', 'author', 'isbn'],
                ],
            ];
        }

        if (isset($params['author']) && $params['author'] !== '') {
            $must[] = [
                'match' => [
                    'author' => $params['author'],
                ],
            ];
        }

        $range = [];
        if (isset($params['year_from']) && $params['year_from'] !== null && $params['year_from'] !== '') {
            $range['gte'] = (int)$params['year_from'];
        }
        if (isset($params['year_to']) && $params['year_to'] !== null && $params['year_to'] !== '') {
            $range['lte'] = (int)$params['year_to'];
        }
        if (!empty($range)) {
            $must[] = ['range' => ['year' => $range]];
        }

        $queryBody = empty($must) ? ['match_all' => (object)[]] : ['bool' => ['must' => $must]];

        $response = $this->client->search([
            'index' => 'books',
            'from'  => $from,
            'size'  => $size,
            'body'  => [
                'query' => $queryBody,
            ],
        ]);

        $ids = [];
        foreach ($response['hits']['hits'] as $hit) {
            $ids[] = (int) $hit['_id'];
        }

        return $ids;
    }

    public function countSearchResultsWithFilters(array $params): int
    {
        $must = [];

        if (!empty($params['query'])) {
            $must[] = [
                'multi_match' => [
                    'query' => $params['query'],
                    'fields' => ['title^2', 'author', 'isbn'],
                ],
            ];
        }

        if (isset($params['author']) && $params['author'] !== '') {
            $must[] = [
                'match' => [
                    'author' => $params['author'],
                ],
            ];
        }

        $range = [];
        if (isset($params['year_from']) && $params['year_from'] !== null && $params['year_from'] !== '') {
            $range['gte'] = (int)$params['year_from'];
        }
        if (isset($params['year_to']) && $params['year_to'] !== null && $params['year_to'] !== '') {
            $range['lte'] = (int)$params['year_to'];
        }
        if (!empty($range)) {
            $must[] = ['range' => ['year' => $range]];
        }

        $queryBody = empty($must)
            ? ['match_all' => (object)[]]
            : ['bool' => ['must' => $must]];

        $response = $this->client->search([
            'index' => 'books',
            'size'  => 0,
            'body'  => [
                'query' => $queryBody,
            ],
        ]);

        return $response['hits']['total']['value'] ?? 0;
    }
}