<?php
require_once 'src/SearchEngine_MySQL.php';

$engine = new SearchEngine();

echo "Testing search functionality...\n\n";

// Test 1: Empty query
$results = $engine->search("");
echo "Test 1 - Empty query: " . (count($results) === 0 ? "PASS" : "FAIL") . "\n";

// Test 2: Normal search
$results = $engine->search("php");
echo "Test 2 - Search 'php': " . (is_array($results) ? "PASS" : "FAIL") . "\n";
echo "   Results found: " . count($results) . "\n";

// Test 3: Non-existent term
$results = $engine->search("xyzabc123nonexistent");
echo "Test 3 - Non-existent term: " . (count($results) === 0 ? "PASS" : "FAIL") . "\n";

echo "\nAll tests complete!\n";
?>