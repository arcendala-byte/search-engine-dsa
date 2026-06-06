<?php
// src/WordGraph.php - Cleaned version without warnings
class WordGraph {
    private array $adjacencyList = [];
    
    public function addRelationship(string $word1, string $word2): void {
        if (!isset($this->adjacencyList[$word1])) {
            $this->adjacencyList[$word1] = [];
        }
        if (!isset($this->adjacencyList[$word2])) {
            $this->adjacencyList[$word2] = [];
        }
        if (!in_array($word2, $this->adjacencyList[$word1])) {
            $this->adjacencyList[$word1][] = $word2;
        }
        if (!in_array($word1, $this->adjacencyList[$word2])) {
            $this->adjacencyList[$word2][] = $word1;
        }
    }
    
    public function getRelatedWords(string $word): array {
        return $this->adjacencyList[strtolower($word)] ?? [];
    }
    
    public function buildFromDocuments(array $documents): void {
        $cooccurrence = [];
        
        foreach ($documents as $doc) {
            $words = array_unique($this->tokenize($doc['content']));
            $wordCount = count($words);
            
            for ($i = 0; $i < $wordCount; $i++) {
                for ($j = $i + 1; $j < $wordCount; $j++) {
                    if (isset($words[$i]) && isset($words[$j])) {
                        $key = $words[$i] . '|' . $words[$j];
                        $cooccurrence[$key] = ($cooccurrence[$key] ?? 0) + 1;
                    }
                }
            }
        }
        
        foreach ($cooccurrence as $pair => $count) {
            if ($count >= 1) {
                $parts = explode('|', $pair);
                if (count($parts) === 2) {
                    $this->addRelationship($parts[0], $parts[1]);
                }
            }
        }
    }
    
    private function tokenize(string $text): array {
        $text = strtolower($text);
        $words = preg_split('/[\s,\.!?;:]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $stopwords = ['the', 'and', 'of', 'to', 'in', 'a', 'is', 'for', 'on', 'with'];
        return array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });
    }
    
    public function getGraphSize(): int {
        return count($this->adjacencyList);
    }
}
?>