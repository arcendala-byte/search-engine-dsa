<?php
// src/TopResultsHeap.php
class TopResultsHeap extends SplMaxHeap {
    // Max heap - highest score comes first
    
    protected function compare($a, $b): int {
        // Compare by relevance score
        return $a['score'] <=> $b['score'];
    }
    
    public function addResult(int $documentId, float $score, string $title): void {
        $this->insert([
            'document_id' => $documentId,
            'score' => $score,
            'title' => $title
        ]);
    }
    
    public function getTopK(int $k): array {
        $results = [];
        $heapCopy = clone $this;
        
        for ($i = 0; $i < $k && !$heapCopy->isEmpty(); $i++) {
            $results[] = $heapCopy->extract();
        }
        
        return $results;
    }
}