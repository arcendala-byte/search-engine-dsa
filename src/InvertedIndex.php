<?php
// src/InvertedIndex.php - Fixed version
class InvertedIndex {
    private array $index = [];
    
    public function addTerm(string $term, int $documentId): void {
        if (!isset($this->index[$term])) {
            $this->index[$term] = [];
        }
        if (!in_array($documentId, $this->index[$term])) {
            $this->index[$term][] = $documentId;
        }
    }
    
    public function getDocumentsForTerm(string $term): array {
        return $this->index[$term] ?? [];
    }
    
    public function searchAnd(array $terms): array {
        // Always return an array, never null
        if (empty($terms)) {
            return [];
        }
        
        $results = null;
        
        foreach ($terms as $term) {
            $docs = $this->getDocumentsForTerm($term);
            
            // If any term has no documents, AND returns nothing
            if (empty($docs)) {
                return [];
            }
            
            if ($results === null) {
                $results = $docs;
            } else {
                $results = array_intersect($results, $docs);
            }
            
            if (empty($results)) {
                return [];
            }
        }
        
        // Ensure we always return an array
        return is_array($results) ? $results : [];
    }
    
    public function getAllTerms(): array {
        return array_keys($this->index);
    }
    
    public function termExists(string $term): bool {
        return isset($this->index[$term]);
    }
    
    public function getIndexSize(): int {
        return count($this->index);
    }
}
?>