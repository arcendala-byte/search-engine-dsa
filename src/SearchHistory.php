<?php
// src/SearchHistory.php
class SearchHistory {
    private array $history = [];  // STACK implementation
    private int $maxSize = 10;
    
    public function push(string $query): void {
        // Push onto stack
        array_push($this->history, [
            'query' => $query,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        // Keep only last 10
        if (count($this->history) > $this->maxSize) {
            array_shift($this->history);
        }
    }
    
    public function pop(): ?array {
        // Pop from stack (undo last search)
        if (!$this->isEmpty()) {
            return array_pop($this->history);
        }
        return null;
    }
    
    public function peek(): ?array {
        // View last search without removing
        if (!$this->isEmpty()) {
            return $this->history[count($this->history) - 1];
        }
        return null;
    }
    
    public function getAll(): array {
        return array_reverse($this->history); // Newest first
    }
    
    public function isEmpty(): bool {
        return empty($this->history);
    }
    
    public function getSize(): int {
        return count($this->history);
    }
}