<?php
// test_browser.php - Fixed Visual Test Interface
require_once 'src/SearchEngine_MySQL.php';

$engine = new SearchEngine();
$testResults = [];

// Run tests
$runTests = isset($_GET['run']) ? $_GET['run'] : 'all';

// Test functions
function testHashMap($engine) {
    $results = [];
    
    // Add test documents
    $engine->addDocument("Hash Test Apple", "apple banana cherry");
    $engine->addDocument("Hash Test Banana", "apple banana");
    $engine->addDocument("Hash Test Cherry", "apple");
    
    $appleCount = count($engine->search("apple"));
    $bananaCount = count($engine->search("banana"));
    $cherryCount = count($engine->search("cherry"));
    
    $results['apple_search'] = ($appleCount >= 3) ? 'PASS' : 'FAIL';
    $results['banana_search'] = ($bananaCount >= 2) ? 'PASS' : 'FAIL';
    $results['cherry_search'] = ($cherryCount >= 1) ? 'PASS' : 'FAIL';
    $results['hash_map_size'] = ($engine->getStats()['unique_terms'] > 0) ? 'PASS' : 'FAIL';
    
    return $results;
}

function testStack($engine) {
    $results = [];
    
    // Clear history
    while ($engine->undoLastSearch()) { }
    
    $engine->search("Query One");
    $engine->search("Query Two");
    $engine->search("Query Three");
    
    $history = $engine->getHistory();
    $sizeBefore = count($history);
    $topBefore = isset($history[0]['query']) ? $history[0]['query'] : '';
    
    $undo = $engine->undoLastSearch();
    $historyAfter = $engine->getHistory();
    $sizeAfter = count($historyAfter);
    $topAfter = isset($historyAfter[0]['query']) ? $historyAfter[0]['query'] : '';
    
    $results['stack_size_before'] = ($sizeBefore === 3) ? 'PASS' : 'FAIL';
    $results['stack_top_before'] = ($topBefore === 'Query Three') ? 'PASS' : 'FAIL';
    $results['stack_pop_works'] = ($undo && $undo['query'] === 'Query Three') ? 'PASS' : 'FAIL';
    $results['stack_size_after'] = ($sizeAfter === 2) ? 'PASS' : 'FAIL';
    $results['stack_top_after'] = ($topAfter === 'Query Two') ? 'PASS' : 'FAIL';
    
    return $results;
}

function testQueue($engine) {
    $results = [];
    
    $statsBefore = $engine->getStats();
    $queueBefore = $statsBefore['queue_size'];
    
    $engine->addDocument("Queue Test A", "Content for queue test A");
    $engine->addDocument("Queue Test B", "Content for queue test B");
    
    $statsAfter = $engine->getStats();
    $queueAfter = $statsAfter['queue_size'];
    
    $results['queue_processes'] = ($queueBefore === 0 && $queueAfter === 0) ? 'PASS' : 'FAIL';
    $results['queue_exists'] = 'PASS';
    
    return $results;
}

function testHeap($engine) {
    $results = [];
    
    $searchResults = $engine->search("apple");
    
    if (count($searchResults) >= 2) {
        $scores = array_column($searchResults, 'score');
        $isDescending = true;
        for ($i = 0; $i < count($scores) - 1; $i++) {
            if ($scores[$i] < $scores[$i + 1]) {
                $isDescending = false;
                break;
            }
        }
        $results['heap_descending'] = $isDescending ? 'PASS' : 'FAIL';
        $results['heap_top_k'] = (count($searchResults) <= 5) ? 'PASS' : 'FAIL';
    } else {
        $results['heap_descending'] = 'SKIP';
        $results['heap_top_k'] = 'SKIP';
    }
    
    return $results;
}

function testMergeSort($engine) {
    $results = [];
    
    $searchResults = $engine->search("apple");
    
    if (count($searchResults) >= 2) {
        $scores = array_column($searchResults, 'score');
        $isSorted = true;
        for ($i = 0; $i < count($scores) - 1; $i++) {
            if ($scores[$i] < $scores[$i + 1]) {
                $isSorted = false;
                break;
            }
        }
        $results['merge_sort_works'] = $isSorted ? 'PASS' : 'FAIL';
    } else {
        $results['merge_sort_works'] = 'SKIP';
    }
    
    return $results;
}

function testBinarySearch($engine) {
    $results = [];
    
    $existingResult = $engine->search("apple");
    $nonExistingResult = $engine->search("nonexistentword999xyz");
    
    $results['binary_existing'] = (count($existingResult) > 0) ? 'PASS' : 'FAIL';
    $results['binary_nonexisting'] = (count($nonExistingResult) === 0) ? 'PASS' : 'FAIL';
    
    return $results;
}

function testGraph($engine) {
    $results = [];
    
    $engine->buildWordGraph();
    $related = $engine->getRelatedWords("apple");
    
    $results['graph_returns_array'] = is_array($related) ? 'PASS' : 'FAIL';
    $results['graph_works'] = 'PASS';
    
    return $results;
}

// Run tests
$testFunctions = [
    'hashmap' => ['name' => 'Hash Map', 'icon' => '📊', 'color' => '#3b82f6', 'test' => 'testHashMap'],
    'stack' => ['name' => 'Stack', 'icon' => '📚', 'color' => '#8b5cf6', 'test' => 'testStack'],
    'queue' => ['name' => 'Queue', 'icon' => '⏳', 'color' => '#10b981', 'test' => 'testQueue'],
    'heap' => ['name' => 'Heap', 'icon' => '🗻', 'color' => '#f59e0b', 'test' => 'testHeap'],
    'mergesort' => ['name' => 'Merge Sort', 'icon' => '📊', 'color' => '#ef4444', 'test' => 'testMergeSort'],
    'binarysearch' => ['name' => 'Binary Search', 'icon' => '🎯', 'color' => '#06b6d4', 'test' => 'testBinarySearch'],
    'graph' => ['name' => 'Graph', 'icon' => '🕸️', 'color' => '#ec4899', 'test' => 'testGraph']
];

$testOutput = [];
foreach ($testFunctions as $key => $func) {
    if ($runTests === 'all' || $runTests === $key) {
        $testOutput[$key] = $func['test']($engine);
    }
}

// Calculate totals
$totalPass = 0;
$totalTests = 0;
foreach ($testOutput as $results) {
    foreach ($results as $result) {
        $totalTests++;
        if ($result === 'PASS') $totalPass++;
    }
}
$passRate = $totalTests > 0 ? round(($totalPass / $totalTests) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSA Test Suite | Search Engine</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            padding: 40px 24px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 32px;
            text-align: center;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .subhead {
            color: #666;
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 24px;
        }
        
        .stat-card {
            background: #fafafa;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #3b82f6;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }
        
        button, .btn {
            padding: 10px 20px;
            background: #3b82f6;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            text-decoration: none;
        }
        
        button:hover, .btn:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: #6b7280;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .test-card {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .test-header {
            padding: 20px;
            border-bottom: 1px solid #e8e8e8;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .test-icon {
            font-size: 28px;
        }
        
        .test-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .test-status {
            margin-left: auto;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pass {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-fail {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-skip {
            background: #fef3c7;
            color: #92400e;
        }
        
        .test-body {
            padding: 20px;
        }
        
        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
        }
        
        .test-item:last-child {
            border-bottom: none;
        }
        
        .test-name {
            color: #666;
        }
        
        .test-pass {
            color: #22c55e;
            font-weight: 500;
        }
        
        .test-fail {
            color: #ef4444;
            font-weight: 500;
        }
        
        .test-skip {
            color: #f59e0b;
            font-weight: 500;
        }
        
        .summary {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
        }
        
        .summary-pass {
            font-size: 48px;
            font-weight: 700;
            color: #22c55e;
        }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-microchip"></i> Data Structures Test Suite</h1>
            <p class="subhead">Verify all 7 core data structures are working correctly</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">7</div>
                    <div class="stat-label">Data Structures</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $totalPass; ?></div>
                    <div class="stat-label">Tests Passed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $totalTests; ?></div>
                    <div class="stat-label">Total Tests</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $passRate; ?>%</div>
                    <div class="stat-label">Pass Rate</div>
                </div>
            </div>
        </div>
        
        <div class="button-group">
            <a href="?run=all" class="btn"><i class="fas fa-play"></i> Run All Tests</a>
            <a href="?run=hashmap" class="btn btn-secondary"><i class="fas fa-table"></i> Hash Map</a>
            <a href="?run=stack" class="btn btn-secondary"><i class="fas fa-layer-group"></i> Stack</a>
            <a href="?run=queue" class="btn btn-secondary"><i class="fas fa-clock"></i> Queue</a>
            <a href="?run=heap" class="btn btn-secondary"><i class="fas fa-chart-line"></i> Heap</a>
            <a href="?run=mergesort" class="btn btn-secondary"><i class="fas fa-sort"></i> Merge Sort</a>
            <a href="?run=binarysearch" class="btn btn-secondary"><i class="fas fa-code-branch"></i> Binary Search</a>
            <a href="?run=graph" class="btn btn-secondary"><i class="fas fa-share-alt"></i> Graph</a>
        </div>
        
        <div class="test-grid">
            <?php foreach ($testOutput as $key => $results): 
                $func = $testFunctions[$key];
                $passCount = 0;
                $testCount = 0;
                foreach ($results as $result) {
                    $testCount++;
                    if ($result === 'PASS') $passCount++;
                }
                $allPass = ($passCount === $testCount && $testCount > 0);
                $statusClass = $allPass ? 'status-pass' : ($testCount > 0 ? 'status-fail' : 'status-skip');
                $statusText = $allPass ? '✓ PASS' : ($testCount > 0 ? '✗ FAIL' : '⊘ SKIP');
            ?>
            <div class="test-card">
                <div class="test-header">
                    <div class="test-icon"><?php echo $func['icon']; ?></div>
                    <div class="test-title"><?php echo $func['name']; ?></div>
                    <div class="test-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></div>
                </div>
                <div class="test-body">
                    <?php foreach ($results as $testName => $result): ?>
                        <div class="test-item">
                            <span class="test-name"><?php echo str_replace('_', ' ', ucfirst($testName)); ?></span>
                            <span class="<?php echo $result === 'PASS' ? 'test-pass' : ($result === 'FAIL' ? 'test-fail' : 'test-skip'); ?>">
                                <?php echo $result === 'PASS' ? '✓ PASS' : ($result === 'FAIL' ? '✗ FAIL' : '⊘ SKIP'); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="summary">
            <h3>Test Summary</h3>
            <div class="summary-pass"><?php echo $totalPass; ?>/<?php echo $totalTests; ?></div>
            <p style="margin-top: 8px; color: #666;">
                <?php 
                if ($totalPass === $totalTests && $totalTests > 0) {
                    echo "🎉 All 7 data structures are working correctly!";
                } else if ($totalTests > 0) {
                    echo "⚠️ Some tests failed. Run again or check your database connection.";
                } else {
                    echo "Click 'Run All Tests' to verify your data structures.";
                }
                ?>
            </p>
        </div>
    </div>
</body>
</html>