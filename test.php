<?php
echo "Testing file paths...\n";
echo "Current directory: " . __DIR__ . "\n";

$files = [
    'config/db_connection.php',
    'src/InvertedIndex.php',
    'src/SearchHistory.php',
    'src/IndexQueue.php',
    'src/TopResultsHeap.php',
    'src/WordGraph.php',
    'src/ResultSorter.php',
    'src/TermSearcher.php',
    'src/SearchEngine_MySQL.php'
];

foreach ($files as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file MISSING\n";
    }
}
?>