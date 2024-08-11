<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Post implements Model {
    use GenericModel;

    public function __construct(
        private string $content,
        private ?int $id = null,
        private ?int $replyToId = null,
        private ?string $subject = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {}

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getReplyToId(): ?int {
        return $this->replyToId;
    }

    public function setReplyToId(?int $replyToId): void {
        $this->replyToId = $replyToId;
    }

    public function getSubject(): ?string {
        return $this->subject;
    }

    public function setSubject(?string $subject): void {
        $this->subject = $subject;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }
}
