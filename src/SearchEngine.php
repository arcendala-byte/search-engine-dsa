<?php
// src/SearchEngine_MySQL.php
require_once __DIR__ . '/../config/db_connection.php';
require_once 'InvertedIndex.php';
require_once 'SearchHistory.php';
require_once 'IndexQueue.php';
require_once 'TopResultsHeap.php';
require_once 'WordGraph.php';
require_once 'ResultSorter.php';
require_once 'TermSearcher.php';

class SearchEngine {
    private $invertedIndex;
    private $history;
    private $queue;
    private $heap;
    private $graph;
    private $sorter;
    private $searcher;
    private $db;
    
    public function __construct() {
        $this->invertedIndex = new InvertedIndex();
        $this->history = new SearchHistory();
        $this->queue = new IndexQueue();
        $this->heap = new TopResultsHeap();
        $this->graph = new WordGraph();
        $this->sorter = new ResultSorter();
        $this->searcher = new TermSearcher();
        
        // Connect to MySQL database
        $this->db = Database::getConnection();
        
        // Load existing index from database
        $this->loadIndexFromDatabase();
    }
    
    public function addDocument($title, $content) {
        try {
            // Insert into documents table
            $stmt = $this->db->prepare("INSERT INTO documents (title, content) VALUES (?, ?)");
            $stmt->execute([$title, $content]);
            $docId = $this->db->lastInsertId();
            
            // Add to queue for indexing
            $this->queue->enqueue([
                'id' => $docId,
                'title' => $title,
                'content' => $content
            ]);
            
            // Process queue immediately
            $this->processQueue();
            
            return $docId;
        } catch (PDOException $e) {
            echo "Error adding document: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function processQueue() {
        $this->queue->processAll(function($doc) {
            $words = $this->tokenize($doc['content']);
            
            foreach ($words as $position => $word) {
                // Add to in-memory inverted index
                $this->invertedIndex->addTerm($word, $doc['id']);
                
                // Store in database
                $stmt = $this->db->prepare(
                    "INSERT INTO term_positions (term, document_id, position) VALUES (?, ?, ?)"
                );
                $stmt->execute([$word, $doc['id'], $position]);
            }
            
            echo "✓ Indexed: {$doc['title']} (" . count($words) . " terms)\n";
        });
    }
    
    public function search($query) {
        // Push to history stack
        $this->history->push($query);
        
        // Save search to database
        $stmt = $this->db->prepare("INSERT INTO search_history (query) VALUES (?)");
        $stmt->execute([$query]);
        
        $terms = $this->tokenize($query);
        
        // Binary search check if terms exist
        $allTerms = $this->searcher->quickSort($this->invertedIndex->getAllTerms());
        foreach ($terms as $term) {
            if (!$this->searcher->binarySearch($allTerms, $term)) {
                echo "⚠️ Term not found: $term\n";
            }
        }
        
        // Search using inverted index (hash map)
        $documentIds = $this->invertedIndex->searchAnd($terms);
        
        if (empty($documentIds)) {
            return [];
        }
        
        // Calculate TF scores
        $scores = $this->calculateScores($documentIds, $terms);
        
        // Add to heap for top-K
        $this->heap = new TopResultsHeap();
        foreach ($scores as $docId => $score) {
            $title = $this->getDocumentTitle($docId);
            $this->heap->addResult($docId, $score, $title);
        }
        
        // Get top 5 results from heap
        $topResults = $this->heap->getTopK(5);
        
        // Sort results using merge sort
        $sortedResults = $this->sorter->mergeSort($topResults);
        
        return $sortedResults;
    }
    
    private function calculateScores($documentIds, $terms) {
        $scores = [];
        
        foreach ($documentIds as $docId) {
            $score = 0;
            foreach ($terms as $term) {
                // Count term frequency in document from database
                $stmt = $this->db->prepare(
                    "SELECT COUNT(*) as count FROM term_positions 
                     WHERE term = ? AND document_id = ?"
                );
                $stmt->execute([$term, $docId]);
                $result = $stmt->fetch();
                $score += $result['count'];
            }
            $scores[$docId] = $score;
        }
        
        return $scores;
    }
    
    private function getDocumentTitle($docId) {
        $stmt = $this->db->prepare("SELECT title FROM documents WHERE id = ?");
        $stmt->execute([$docId]);
        $result = $stmt->fetch();
        return $result ? $result['title'] : "Unknown";
    }
    
    public function getAllDocuments() {
        $stmt = $this->db->query("SELECT id, title, content, created_at FROM documents ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function getDocumentCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM documents");
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function getSearchHistory() {
        $stmt = $this->db->query("SELECT query, searched_at FROM search_history ORDER BY searched_at DESC LIMIT 10");
        return $stmt->fetchAll();
    }
    
    public function getHistory() {
        return $this->history->getAll();
    }
    
    public function undoLastSearch() {
        return $this->history->pop();
    }
    
    public function getStats() {
        return [
            'unique_terms' => $this->invertedIndex->getIndexSize(),
            'queue_size' => $this->queue->getSize(),
            'history_size' => $this->history->getSize(),
            'documents_count' => $this->getDocumentCount()
        ];
    }
    
    private function tokenize($text) {
        $text = strtolower($text);
        $words = preg_split('/[\s,\.!?;:]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $stopwords = ['the', 'and', 'of', 'to', 'in', 'a', 'is', 'for', 'on', 'with', 'by', 'at', 'an'];
        return array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });
    }
    
    private function loadIndexFromDatabase() {
        try {
            $stmt = $this->db->query("SELECT term, document_id FROM term_positions");
            while ($row = $stmt->fetch()) {
                $this->invertedIndex->addTerm($row['term'], $row['document_id']);
            }
            echo "📚 Loaded {$this->invertedIndex->getIndexSize()} unique terms from database\n";
        } catch (PDOException $e) {
            // Table might not exist yet - that's OK
        }
    }
    
    // Display all data structures status
    public function showDataStructuresStatus() {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 DATA STRUCTURES STATUS\n";
        echo str_repeat("=", 60) . "\n";
        
        echo "✅ Hash Map (Inverted Index):\n";
        echo "   → {$this->invertedIndex->getIndexSize()} unique terms indexed\n";
        echo "   → O(1) lookup time\n\n";
        
        echo "✅ Stack (Search History):\n";
        echo "   → {$this->history->getSize()} searches in memory\n";
        echo "   → LIFO operations: push/pop O(1)\n\n";
        
        echo "✅ Queue (Indexing Jobs):\n";
        echo "   → {$this->queue->getSize()} documents pending\n";
        echo "   → FIFO operations: enqueue/dequeue O(1)\n\n";
        
        echo "✅ Heap (Top-K Results):\n";
        echo "   → Priority queue ready\n";
        echo "   → Extract max in O(log n)\n\n";
        
        echo "✅ Graph (Word Relationships):\n";
        echo "   → Adjacency list ready\n";
        echo "   → BFS/DFS traversal ready\n\n";
        
        echo "✅ Sorting (Merge Sort):\n";
        echo "   → O(n log n) time complexity\n";
        echo "   → Stable sorting algorithm\n\n";
        
        echo "✅ Binary Search:\n";
        echo "   → O(log n) search time\n";
        echo "   → Requires sorted array\n\n";
        
        echo str_repeat("=", 60) . "\n";
    }
}
?>