<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class BookSearch extends AbstractCommand
{
    protected static ?string $alias = 'book-search';

    public static function getArguments(): array
    {
        return [
            (new Argument('title'))
                ->description('Search by book title')
                ->required(false)
                ->allowAsShort(true),
            (new Argument('isbn'))
                ->description('Search by ISBN')
                ->required(false)
                ->allowAsShort(true),
        ];
    }

    public function execute(): int
    {
        $title = $this->getArgumentValue('title');
        $isbn = $this->getArgumentValue('isbn');

        if (!$title && !$isbn) {
            $this->log("Error: Please provide either a title or ISBN to search.");
            return 1;
        }

        $result = $this->searchBook($title, $isbn);

        if ($result) {
            $this->displayResult($result);
        } else {
            $this->log("No results found.");
        }

        return 0;
    }

    private function searchBook(?string $title, ?string $isbn): ?array
    {
        $cacheResult = $this->searchCache($title, $isbn);

        if ($cacheResult) {
            return $cacheResult;
        }

        // キャッシュにない場合、Open Library APIを使用して検索
        $apiResult = $this->searchOpenLibrary($title, $isbn);

        if ($apiResult) {
            // 結果をキャッシュに保存
            $this->cacheResult($apiResult);
            return $apiResult;
        }

        return null;
    }

    private function searchCache(?string $title, ?string $isbn): ?array
    {
        return null;
    }

    private function searchOpenLibrary(?string $title, ?string $isbn): ?array
    {
        $query = $isbn ? "isbn:$isbn" : "title:" . urlencode($title);
        $url = "https://openlibrary.org/search.json?q=$query";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['docs'][0])) {
            return $data['docs'][0];
        }

        return null;
    }

    private function cacheResult(array $result): void
    {}

    private function displayResult(array $result): void
    {
        $this->log("Book found:");
        $this->log("Title: " . ($result['title'] ?? 'N/A'));
        $this->log("Author: " . implode(", ", $result['author_name'] ?? ['N/A']));
        $this->log("ISBN: " . ($result['isbn'] ? implode(", ", $result['isbn']) : 'N/A'));
        $this->log("First Published Year: " . ($result['first_publish_year'] ?? 'N/A'));
    }
}