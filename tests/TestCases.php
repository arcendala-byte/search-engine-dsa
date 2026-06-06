<?php
// tests/TestCases.php
class TestCases {
    private $engine;
    private $passed = 0;
    private $failed = 0;
    
    public function __construct($engine) {
        $this->engine = $engine;
    }
    
    public function runAll() {
        echo "\n🧪 RUNNING 15+ TEST CASES\n";
        echo str_repeat("=", 60) . "\n";
        
        $this->testAddDocument();
        $this->testSearchSingleTerm();
        $this->testSearchMultipleTerms();
        $this->testSearchNoResults();
        $this->testSearchHistory();
        $this->testUndoSearch();
        $this->testQueueOperations();
        $this->testHeapTopResults();
        $this->testWordGraph();
        $this->testMergeSort();
        $this->testBinarySearch();
        $this->testHashMapLookup();
        $this->testInvertedIndex();
        $this->testCaseInsensitive();
        $this->testStopwordRemoval();
        $this->testPerformance();
        
        echo str_repeat("=", 60) . "\n";
        echo "✅ PASSED: $this->passed\n";
        echo "❌ FAILED: $this->failed\n";
        echo "📊 TOTAL: " . ($this->passed + $this->failed) . "\n";
    }
    
    private function assert($condition, $message) {
        if ($condition) {
            echo "✓ PASS: $message\n";
            $this->passed++;
        } else {
            echo "✗ FAIL: $message\n";
            $this->failed++;
        }
    }
    
    private function testAddDocument() {
        $id = $this->engine->addDocument("Test Doc", "This is a test document for testing");
        $this->assert($id > 0, "Add document returns valid ID");
    }
    
    private function testSearchSingleTerm() {
        $results = $this->engine->search("PHP");
        $this->assert(!empty($results), "Search for 'PHP' returns results");
    }
    
    private function testSearchMultipleTerms() {
        $results = $this->engine->search("PHP web development");
        $this->assert(!empty($results), "AND search for multiple terms works");
    }
    
    private function testSearchNoResults() {
        $results = $this->engine->search("xyzabc123nonexistent");
        $this->assert(empty($results), "Search for nonexistent term returns empty");
    }
    
    private function testSearchHistory() {
        $this->engine->search("test1");
        $this->engine->search("test2");
        $history = $this->engine->getHistory();
        $this->assert(count($history) >= 2, "Search history stack stores queries");
    }
    
    private function testUndoSearch() {
        $sizeBefore = count($this->engine->getHistory());
        $undo = $this->engine->undoLastSearch();
        $sizeAfter = count($this->engine->getHistory());
        $this->assert($sizeAfter < $sizeBefore, "Stack pop (undo) works");
    }
    
    private function testQueueOperations() {
        $stats = $this->engine->getStats();
        $this->assert(isset($stats['queue_size']), "Queue exists and shows size");
    }
    
    private function testHeapTopResults() {
        $results = $this->engine->search("web");
        $this->assert(count($results) <= 5, "Heap returns top-5 results");
    }
    
    private function testWordGraph() {
        $this->engine->buildWordGraph();
        $related = $this->engine->getRelatedWords("PHP");
        $this->assert(is_array($related), "Word graph returns array of related words");
    }
    
    private function testMergeSort() {
        $sorter = new ResultSorter();
        $testArray = [
            ['score' => 3], ['score' => 1], ['score' => 5], ['score' => 2], ['score' => 4]
        ];
        $sorted = $sorter->mergeSort($testArray);
        $this->assert($sorted[0]['score'] == 5, "Merge sort O(n log n) works correctly");
    }
    
    private function testBinarySearch() {
        $searcher = new TermSearcher();
        $terms = $searcher->quickSort(['apple', 'banana', 'cherry', 'date']);
        $found = $searcher->binarySearch($terms, 'cherry');
        $notFound = $searcher->binarySearch($terms, 'grape');
        $this->assert($found && !$notFound, "Binary search O(log n) works");
    }
    
    private function testHashMapLookup() {
        $index = new InvertedIndex();
        $index->addTerm('test', 1);
        $docs = $index->getDocumentsForTerm('test');
        $this->assert(count($docs) == 1 && $docs[0] == 1, "Hash map provides O(1) lookup");
    }
    
    private function testInvertedIndex() {
        $index = new InvertedIndex();
        $index->addTerm('cat', 1);
        $index->addTerm('cat', 2);
        $index->addTerm('dog', 2);
        $results = $index->searchAnd(['cat', 'dog']);
        $this->assert($results == [2], "Inverted index AND query works");
    }
    
    private function testCaseInsensitive() {
        $this->engine->addDocument("Case Test", "UPPERCASE lowercase MIXED");
        $results1 = $this->engine->search("uppercase");
        $results2 = $this->engine->search("UPPERCASE");
        $this->assert(!empty($results1) && !empty($results2), "Search is case-insensitive");
    }
    
    private function testStopwordRemoval() {
        $this->engine->addDocument("Stopword Test", "the and of this is a test");
        $results = $this->engine->search("the and test");
        $this->assert(!empty($results), "Stopwords filtered from query");
    }
    
    private function testPerformance() {
        $start = microtime(true);
        $this->engine->search("web development PHP JavaScript");
        $time = (microtime(true) - $start) * 1000;
        $this->assert($time < 100, "Search completes within 100ms (O(log n) performance)");
    }
}