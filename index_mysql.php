<?php
// index_mysql.php - Search Engine with MySQL
require_once 'src/SearchEngine_MySQL.php';

echo "\n";
echo "╔" . str_repeat("═", 58) . "╗\n";
echo "║" . str_repeat(" ", 10) . "🔍 BASIC SEARCH ENGINE with MySQL" . str_repeat(" ", 14) . "║\n";
echo "╠" . str_repeat("═", 58) . "╣\n";
echo "║  Hash Map │ Stack │ Queue │ Heap │ Graph │ Sorting │ Binary Search  ║\n";
echo "╚" . str_repeat("═", 58) . "╝\n";

$engine = new SearchEngine();

// Show data structures status
$engine->showDataStructuresStatus();

// Add sample documents if database is empty
if ($engine->getDocumentCount() == 0) {
    echo "\n📚 Loading sample documents...\n";
    $engine->addDocument("PHP Tutorial", "PHP is a popular scripting language for web development. It is used to create dynamic web pages.");
    $engine->addDocument("JavaScript Guide", "JavaScript is used for interactive web pages. It runs in the browser.");
    $engine->addDocument("Database Design", "SQL databases store structured data efficiently. MySQL is a popular database.");
    $engine->addDocument("Web Development", "HTML CSS and JavaScript are core web technologies. PHP is used for backend.");
    $engine->addDocument("Data Structures", "Arrays linked lists stacks queues trees and graphs are fundamental data structures.");
    echo "✓ 5 sample documents loaded\n";
}

while (true) {
    echo "\n" . str_repeat("─", 50) . "\n";
    echo "📋 MAIN MENU\n";
    echo str_repeat("─", 50) . "\n";
    echo "1. ➕ Add Document\n";
    echo "2. 🔍 Search\n";
    echo "3. 📜 View Search History (Stack)\n";
    echo "4. ↩️  Undo Last Search (Stack Pop)\n";
    echo "5. 📋 View Queue Status\n";
    echo "6. 📊 View All Documents\n";
    echo "7. 📈 View System Statistics\n";
    echo "8. 🧪 Run Tests\n";
    echo "9. ℹ️  Data Structures Info\n";
    echo "10. 🚪 Exit\n";
    echo str_repeat("─", 50) . "\n";
    echo "Choice: ";
    
    $choice = trim(fgets(STDIN));
    
    switch ($choice) {
        case '1':
            echo "\n--- ADD NEW DOCUMENT ---\n";
            echo "Title: ";
            $title = trim(fgets(STDIN));
            echo "Content: ";
            $content = trim(fgets(STDIN));
            
            if (empty($title) || empty($content)) {
                echo "❌ Title and content cannot be empty!\n";
            } else {
                $id = $engine->addDocument($title, $content);
                if ($id) {
                    echo "✅ Document added successfully! (ID: $id)\n";
                }
            }
            break;
            
        case '2':
            echo "\n--- SEARCH ---\n";
            echo "Enter search query (e.g., 'PHP database'): ";
            $query = trim(fgets(STDIN));
            
            if (empty($query)) {
                echo "❌ Query cannot be empty!\n";
                break;
            }
            
            echo "\n🔍 Searching for: \"$query\"\n";
            echo str_repeat("•", 50) . "\n";
            
            $results = $engine->search($query);
            
            if (empty($results)) {
                echo "❌ No results found.\n";
            } else {
                echo "📊 Top Results (Heap + Merge Sort):\n\n";
                foreach ($results as $i => $result) {
                    echo ($i + 1) . ". 📄 {$result['title']}\n";
                    echo "   ⭐ Score: {$result['score']}\n";
                    echo "   📍 Document ID: {$result['document_id']}\n\n";
                }
            }
            echo str_repeat("•", 50) . "\n";
            break;
            
        case '3':
            echo "\n--- SEARCH HISTORY (Stack - LIFO) ---\n";
            $history = $engine->getHistory();
            if (empty($history)) {
                echo "📭 No search history yet.\n";
            } else {
                echo "Recent searches (most recent first):\n";
                foreach ($history as $i => $item) {
                    echo ($i + 1) . ". \"{$item['query']}\" at {$item['timestamp']}\n";
                }
                echo "\n💡 Stack operations: push() on search, pop() on undo\n";
            }
            break;
            
        case '4':
            echo "\n--- UNDO LAST SEARCH (Stack Pop) ---\n";
            $undo = $engine->undoLastSearch();
            if ($undo) {
                echo "✅ Undid search: \"{$undo['query']}\" from {$undo['timestamp']}\n";
                echo "💡 Stack pop() removed the last search from history\n";
            } else {
                echo "❌ Nothing to undo. Stack is empty.\n";
            }
            break;
            
        case '5':
            $stats = $engine->getStats();
            echo "\n--- QUEUE STATUS ---\n";
            echo "📋 Documents waiting to be indexed: {$stats['queue_size']}\n";
            echo "💡 Queue is FIFO (First In, First Out)\n";
            echo "💡 enqueue() adds to back, dequeue() removes from front\n";
            break;
            
        case '6':
            echo "\n--- ALL DOCUMENTS ---\n";
            $documents = $engine->getAllDocuments();
            if (empty($documents)) {
                echo "📭 No documents found.\n";
            } else {
                foreach ($documents as $doc) {
                    echo "📄 ID: {$doc['id']} | Title: {$doc['title']}\n";
                    echo "   Created: {$doc['created_at']}\n";
                    echo "   Preview: " . substr($doc['content'], 0, 100) . "...\n\n";
                }
            }
            break;
            
        case '7':
            $stats = $engine->getStats();
            echo "\n--- SYSTEM STATISTICS ---\n";
            echo str_repeat("•", 40) . "\n";
            echo "📚 Total Documents: {$stats['documents_count']}\n";
            echo "📖 Unique Terms: {$stats['unique_terms']}\n";
            echo "📋 Queue Size: {$stats['queue_size']}\n";
            echo "📜 Search History Size: {$stats['history_size']}\n";
            echo str_repeat("•", 40) . "\n";
            break;
            
        case '8':
            echo "\n--- RUNNING TESTS ---\n";
            $passed = 0;
            $total = 7;
            
            echo "Test 1: Adding document... ";
            $id = $engine->addDocument("Test Doc", "This is a test document for testing purposes.");
            if ($id) { $passed++; echo "✅ PASS\n"; }
            else { echo "❌ FAIL\n"; }
            
            echo "Test 2: Searching... ";
            $results = $engine->search("test");
            if (!empty($results)) { $passed++; echo "✅ PASS\n"; }
            else { echo "❌ FAIL\n"; }
            
            echo "Test 3: Hash Map (Inverted Index)... ";
            $stats = $engine->getStats();
            if ($stats['unique_terms'] > 0) { $passed++; echo "✅ PASS\n"; }
            else { echo "❌ FAIL\n"; }
            
            echo "Test 4: Stack (History)... ";
            $history = $engine->getHistory();
            if (!empty($history)) { $passed++; echo "✅ PASS\n"; }
            else { echo "❌ FAIL\n"; }
            
            echo "Test 5: Queue... ";
            if (isset($stats['queue_size'])) { $passed++; echo "✅ PASS\n"; }
            else { echo "❌ FAIL\n"; }
            
            echo "Test 6: Heap (Top Results)... ";
            $results = $engine->search("PHP");
            if (count($results) <= 5) { $passed++; echo "✅ PASS\n"; }
            else { echo "❌ FAIL\n"; }
            
            echo "Test 7: Binary Search... ";
            $searcher = new TermSearcher();
            $terms = ['apple', 'banana', 'cherry'];
            if ($searcher->binarySearch($terms, 'banana')) { $passed++; echo "✅ PASS\n"; }
            else { echo "❌ FAIL\n"; }
            
            echo "\n📊 Test Results: $passed/$total passed\n";
            if ($passed == $total) {
                echo "🎉 All tests passed!\n";
            }
            break;
            
        case '9':
            $engine->showDataStructuresStatus();
            break;
            
        case '10':
            echo "\n👋 Goodbye!\n";
            echo "Thanks for using the Search Engine System!\n";
            exit(0);
            
        default:
            echo "❌ Invalid choice. Please enter 1-10.\n";
    }
}
?>