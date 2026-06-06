<?php
// src/SearchEngine_MySQL.php - Complete with Persistent History
require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/InvertedIndex.php';
require_once __DIR__ . '/SearchHistory.php';
require_once __DIR__ . '/IndexQueue.php';
require_once __DIR__ . '/TopResultsHeap.php';
require_once __DIR__ . '/WordGraph.php';
require_once __DIR__ . '/ResultSorter.php';
require_once __DIR__ . '/TermSearcher.php';

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
        
        // Create search_history table if not exists
        $this->createHistoryTable();
        
        // Load existing index from database
        $this->loadIndexFromDatabase();
        
        // Build word graph from existing documents
        $this->buildWordGraph();
    }
    
    // Create the persistent history table
    private function createHistoryTable() {
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS search_history (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    query VARCHAR(255) NOT NULL,
                    search_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    results_count INT DEFAULT 0,
                    INDEX idx_search_time (search_time)
                )
            ");
        } catch (PDOException $e) {
            // Table already exists or error - ignore
        }
    }
    
    // Save search to persistent history
    public function saveSearchToHistory($query, $resultCount = 0) {
        try {
            $stmt = $this->db->prepare("INSERT INTO search_history (query, results_count, search_time) VALUES (?, ?, NOW())");
            $stmt->execute([$query, $resultCount]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Get persistent search history from database
    public function getPersistentHistory($limit = 20) {
        try {
            $stmt = $this->db->prepare("SELECT id, query, results_count, search_time FROM search_history ORDER BY search_time DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Delete single search from history
    public function deleteSearchFromHistory($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM search_history WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Clear all search history
    public function clearAllHistory() {
        try {
            $stmt = $this->db->prepare("DELETE FROM search_history");
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Get total search count
    public function getTotalSearchCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM search_history");
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function addDocument($title, $content) {
        try {
            // Validate input
            if (empty($title) || empty($content)) {
                return false;
            }
            
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
            
            // Rebuild word graph
            $this->buildWordGraph();
            
            return $docId;
        } catch (PDOException $e) {
            error_log("Error adding document: " . $e->getMessage());
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
            
            if (php_sapi_name() === 'cli') {
                echo "✓ Indexed: {$doc['title']} (" . count($words) . " terms)\n";
            }
        });
    }
    
    public function search($query) {
        // Handle empty query
        if (empty($query)) {
            return [];
        }
        
        // Push to memory stack (for undo feature)
        $this->history->push($query);
        
        $terms = $this->tokenize($query);
        
        // If no valid terms after tokenization
        if (empty($terms)) {
            // Save to persistent history with 0 results
            $this->saveSearchToHistory($query, 0);
            return [];
        }
        
        // Binary search check if terms exist
        $allTerms = $this->searcher->quickSort($this->invertedIndex->getAllTerms());
        foreach ($terms as $term) {
            if (!$this->searcher->binarySearch($allTerms, $term)) {
                if (php_sapi_name() === 'cli') {
                    echo "⚠️ Term not found: $term\n";
                }
            }
        }
        
        // Search using inverted index (hash map)
        $documentIds = $this->invertedIndex->searchAnd($terms);
        
        if (empty($documentIds)) {
            // Save to persistent history with 0 results
            $this->saveSearchToHistory($query, 0);
            return [];
        }
        
        // Calculate TF scores
        $scores = $this->calculateScores($documentIds, $terms);
        
        if (empty($scores)) {
            $this->saveSearchToHistory($query, 0);
            return [];
        }
        
        // Add to heap for top-K
        $this->heap = new TopResultsHeap();
        foreach ($scores as $docId => $score) {
            $title = $this->getDocumentTitle($docId);
            $this->heap->addResult($docId, $score, $title);
        }
        
        // Get top 5 results from heap
        $topResults = $this->heap->getTopK(5);
        
        // Remove duplicates (fix for duplicate results)
        $uniqueResults = [];
        $seenTitles = [];
        foreach ($topResults as $result) {
            if (!in_array($result['title'], $seenTitles)) {
                $uniqueResults[] = $result;
                $seenTitles[] = $result['title'];
            }
        }
        
        // Sort results using merge sort
        $sortedResults = $this->sorter->mergeSort($uniqueResults);
        
        // Save to persistent history with result count
        $this->saveSearchToHistory($query, count($sortedResults));
        
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
                $score += $result ? $result['count'] : 0;
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
        return $result ? $result['count'] : 0;
    }
    
    // Legacy method - kept for compatibility
    public function getSearchHistory() {
        return $this->getPersistentHistory(10);
    }
    
    // Memory stack methods (for undo feature)
    public function getHistory() {
        return $this->history->getAll();
    }
    
    public function undoLastSearch() {
        return $this->history->pop();
    }
    
    public function getRelatedWords($word) {
        return $this->graph->getRelatedWords($word);
    }
    
    public function buildWordGraph() {
        $documents = $this->getAllDocuments();
        $this->graph->buildFromDocuments($documents);
    }
    
    public function getTopResultsDemo() {
        $results = $this->search("computer");
        return $this->heap->getTopK(3);
    }
    
    public function getStats() {
        return [
            'unique_terms' => $this->invertedIndex->getIndexSize(),
            'queue_size' => $this->queue->getSize(),
            'history_size' => $this->history->getSize(),
            'documents_count' => $this->getDocumentCount(),
            'total_searches' => $this->getTotalSearchCount()
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
            if (php_sapi_name() === 'cli') {
                echo "📚 Loaded {$this->invertedIndex->getIndexSize()} unique terms from database\n";
            }
        } catch (PDOException $e) {
            // Table might not exist yet - that's OK
            if (php_sapi_name() === 'cli') {
                echo "No existing index found. Starting fresh.\n";
            }
        }
    }
    
    public function showDataStructuresStatus() {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "DATA STRUCTURES STATUS\n";
        echo str_repeat("=", 60) . "\n";
        
        echo "Hash Map (Inverted Index):\n";
        echo "   → " . $this->invertedIndex->getIndexSize() . " unique terms indexed\n";
        echo "   → O(1) lookup time\n\n";
        
        echo "Stack (Search History - Memory):\n";
        echo "   → " . $this->history->getSize() . " searches in memory\n";
        echo "   → LIFO operations: push/pop O(1)\n\n";
        
        echo "Queue (Indexing Jobs):\n";
        echo "   → " . $this->queue->getSize() . " documents pending\n";
        echo "   → FIFO operations: enqueue/dequeue O(1)\n\n";
        
        echo "Heap (Top-K Results):\n";
        echo "   → Priority queue ready\n";
        echo "   → Extract max in O(log n)\n\n";
        
        echo "Graph (Word Relationships):\n";
        echo "   → Adjacency list ready\n";
        echo "   → BFS/DFS traversal ready\n\n";
        
        echo "Sorting (Merge Sort):\n";
        echo "   → O(n log n) time complexity\n";
        echo "   → Stable sorting algorithm\n\n";
        
        echo "Binary Search:\n";
        echo "   → O(log n) search time\n";
        echo "   → Requires sorted array\n\n";
        
        echo "Persistent Database History:\n";
        echo "   → " . $this->getTotalSearchCount() . " total searches stored\n";
        echo "   → You control deletion (delete single or clear all)\n\n";
        
        echo str_repeat("=", 60) . "\n";
    }
}
?>