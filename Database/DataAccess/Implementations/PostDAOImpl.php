<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\PostDAO;
use Database\DatabaseManager;
use Models\Post;

class PostDAOImpl implements PostDAO
{
    public function create(Post $postData): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "INSERT INTO Posts (reply_to_id, subject, content, image_path) VALUES (?, ?, ?, ?)";
        $result = $mysqli->prepareAndExecute($query, 'isss', [
            $postData->getReplyToId(),
            $postData->getSubject(),
            $postData->getContent(),
            $postData->getImagePath()
        ]);
        error_log("Post creation result: " . ($result ? "success" : "failure"));
        error_log("Image path: " . ($postData->getImagePath() ?? "null"));
        return $result;
    }

    public function getById(int $id): ?Post
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $result = $mysqli->prepareAndFetchAll("SELECT * FROM Posts WHERE post_id = ?", 'i', [$id]);
        return $result ? $this->resultToPost($result[0]) : null;
    }

    public function update(Post $postData): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "UPDATE Posts SET subject = ?, content = ?, image_path = ? WHERE post_id = ?";
        return $mysqli->prepareAndExecute($query, 'sssi', [
            $postData->getSubject(),
            $postData->getContent(),
            $postData->getImagePath(),
            $postData->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM Posts WHERE post_id = ?", 'i', [$id]);
    }

    public function createOrUpdate(Post $postData): bool
    {
        return $postData->getId() ? $this->update($postData) : $this->create($postData);
    }

    public function getAllThreads(int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM Posts WHERE reply_to_id IS NULL ORDER BY created_at DESC LIMIT ?, ?";
        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$offset, $limit]);
        return array_map([$this, 'resultToPost'], $results);
    }

    public function getReplies(Post $postData, int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM Posts WHERE reply_to_id = ? ORDER BY created_at ASC LIMIT ?, ?";
        $results = $mysqli->prepareAndFetchAll($query, 'iii', [$postData->getId(), $offset, $limit]);
        return array_map([$this, 'resultToPost'], $results);
    }

    private function resultToPost(array $data): Post
    {
        return new Post(
            id: $data['post_id'],
            replyToId: $data['reply_to_id'],
            subject: $data['subject'],
            content: $data['content'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
            imagePath: $data['image_path']
        );
    }
}