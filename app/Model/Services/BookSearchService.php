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

        $queryBody = $this->buildFilters($params);

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

        $queryBody = $this->buildFilters($params);

        $response = $this->client->search([
            'index' => 'books',
            'size'  => 0,
            'body'  => [
                'query' => $queryBody,
            ],
        ]);

        return $response['hits']['total']['value'] ?? 0;
    }

    public function getAuthorsAggregation(array $params, int $size = 100): array
    {
        $queryBody = $this->buildFilters($params);

        $response = $this->client->search([
            'index' => 'books',
            'size'  => 0,
            'body'  => [
                'query' => $queryBody,
                'aggs' => [
                    'authors' => [
                        'terms' => [
                            'field' => 'author.keyword',
                            'size'  => $size,
                            'order' => ['_count' => 'desc'],
                        ],
                    ],
                ],
            ],
        ]);

        $buckets = $response['aggregations']['authors']['buckets'] ?? [];
        $result = [];
        foreach ($buckets as $b) {
            $result[$b['key']] = (int) $b['doc_count'];
        }

        return $result;
    }

    private function buildFilters(array $params): array
    {
        $must  = [];
        $range = [];

        if (!empty($params['query'])) {
            $must[] = [
                'multi_match' => [
                    'query'  => $params['query'],
                    'fields' => ['title^2', 'author', 'isbn'],
                ],
            ];
        }

        if (!empty($params['author'])) {
            if (is_array($params['author'])) {
                $must[] = [
                    'terms' => ['author.keyword' => array_values($params['author'])],
                ];
            } else {
                $must[] = [
                    'match' => ['author' => $params['author']],
                ];
            }
        }

        if (!empty($params['year_from'])) {
            $range['gte'] = (int) $params['year_from'];
        }
        if (!empty($params['year_to'])) {
            $range['lte'] = (int) $params['year_to'];
        }
        if ($range) {
            $must[] = ['range' => ['year' => $range]];
        }

        return empty($must)
            ? ['match_all' => (object)[]]
            : ['bool' => ['must' => $must]];
    }
}