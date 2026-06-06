<?php
// src/IndexQueue.php
class IndexQueue {
    private SplQueue $queue;  // QUEUE implementation
    
    public function __construct() {
        $this->queue = new SplQueue();
    }
    
    public function enqueue(array $document): void {
        // Add to back of queue
        $this->queue->enqueue($document);
        echo "[Queue] Document '{$document['title']}' added to indexing queue (Position: {$this->queue->count()})\n";
    }
    
    public function dequeue(): ?array {
        // Remove from front of queue (FIFO)
        if (!$this->isEmpty()) {
            $doc = $this->queue->dequeue();
            echo "[Queue] Processing document '{$doc['title']}' (Remaining: {$this->queue->count()})\n";
            return $doc;
        }
        return null;
    }
    
    public function isEmpty(): bool {
        return $this->queue->isEmpty();
    }
    
    public function getSize(): int {
        return $this->queue->count();
    }
    
    public function processAll(callable $processor): void {
        while (!$this->isEmpty()) {
            $doc = $this->dequeue();
            $processor($doc);
        }
    }
}