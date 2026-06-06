<?php
// index_web.php - Professional Search Engine with Complexity Visualization
require_once 'src/SearchEngine_MySQL.php';

$engine = new SearchEngine();

$message = '';
$messageType = '';
$searchResults = [];
$documents = [];
$history = [];
$stats = [];
$currentQuery = '';
$queryTime = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startTime = microtime(true);
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                if ($title && $content) {
                    $id = $engine->addDocument($title, $content);
                    $message = "Document added. ID: $id";
                    $messageType = 'success';
                } else {
                    $message = "Title and content required";
                    $messageType = 'error';
                }
                break;
                
            case 'search':
                $currentQuery = trim($_POST['query'] ?? '');
                if ($currentQuery) {
                    $searchResults = $engine->search($currentQuery);
                    $message = "Found " . count($searchResults) . " result(s)";
                    $messageType = count($searchResults) > 0 ? 'success' : 'warning';
                }
                break;
                
            case 'undo':
                $undo = $engine->undoLastSearch();
                if ($undo) {
                    $message = "Undid: " . htmlspecialchars($undo['query']);
                    $messageType = 'success';
                } else {
                    $message = "History is empty";
                    $messageType = 'warning';
                }
                break;
                
            case 'clear_history':
                $cleared = 0;
                while ($engine->undoLastSearch()) {
                    $cleared++;
                }
                $message = "Cleared $cleared search(es)";
                $messageType = 'success';
                break;
        }
    }
    
    $queryTime = round((microtime(true) - $startTime) * 1000, 2);
}

$documents = $engine->getAllDocuments();
$history = $engine->getHistory();
$stats = $engine->getStats();
$recentQueries = array_unique(array_column($history, 'query'));
$recentQueries = array_slice($recentQueries, 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Engine | DSA Project</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            line-height: 1.5;
            font-weight: 400;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        .container {
            animation: fadeIn 300ms ease-out;
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 24px;
        }
        
        /* Header Section */
        .header-section {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 32px;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a1a;
            letter-spacing: -0.3px;
        }
        
        .subhead {
            color: #666666;
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        /* Badge Container */
        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 20px 0;
            padding-bottom: 20px;
            border-bottom: 1px solid #e8e8e8;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            color: #444;
            transition: all 0.2s ease;
        }
        
        .badge i {
            font-size: 12px;
            color: #3b82f6;
        }
        
        .badge:hover {
            background: #ffffff;
            border-color: #3b82f6;
            transform: translateY(-1px);
        }
        
        .badge-accent {
            background: #eef2ff;
            border-color: #3b82f6;
            color: #1e40af;
        }
        
        .stats-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 20px;
        }
        
        .stat-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 13px;
        }
        
        .stat-pill i {
            font-size: 16px;
            color: #3b82f6;
        }
        
        .stat-pill strong {
            color: #1a1a1a;
            font-size: 18px;
            margin-right: 4px;
        }
        
        /* Grid Layout */
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }
        
        /* Cards */
        .card {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.2s ease;
        }
        
        .card:hover {
            border-color: #d0d0d0;
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #e8e8e8;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        
        .card-header i {
            font-size: 20px;
            color: #3b82f6;
        }
        
        .card-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }
        
        /* Form Elements */
        input, textarea, button {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }
        
        input, textarea {
            width: 100%;
            padding: 10px 12px;
            background: #ffffff;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            color: #1a1a1a;
            transition: all 0.2s ease;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #3b82f6;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        button {
            padding: 10px 16px;
            background: #3b82f6;
            border: none;
            border-radius: 6px;
            color: #ffffff;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        
        button i {
            font-size: 13px;
        }
        
        button:hover {
            background: #2563eb;
        }
        
        button.secondary {
            background: #6b7280;
        }
        
        button.secondary:hover {
            background: #4b5563;
        }
        
        button.danger {
            background: #ef4444;
        }
        
        button.danger:hover {
            background: #dc2626;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
        }
        
        .button-group button {
            flex: 1;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        
        .stat-card {
            background: #fafafa;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            transition: all 0.2s ease;
        }
        
        .stat-card:hover {
            background: #ffffff;
            border-color: #3b82f6;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 11px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .progress-bar {
            background: #e8e8e8;
            border-radius: 4px;
            height: 3px;
            margin-top: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: #3b82f6;
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 4px;
        }
        
        /* Complexity Visualization */
        .complexity-container {
            margin-top: 16px;
        }
        
        .complexity-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .complexity-item:last-child {
            border-bottom: none;
        }
        
        .complexity-info {
            flex: 2;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .complexity-info i {
            width: 20px;
            color: #3b82f6;
            font-size: 14px;
        }
        
        .complexity-name {
            font-size: 13px;
            font-weight: 500;
            color: #1a1a1a;
        }
        
        .complexity-badge {
            font-family: monospace;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            background: #f0f0f0;
        }
        
        .complexity-badge.o1 { background: #dcfce7; color: #166534; }
        .complexity-badge.ologn { background: #dbeafe; color: #1e40af; }
        .complexity-badge.onlogn { background: #fef3c7; color: #92400e; }
        
        .complexity-bar-container {
            flex: 3;
            margin-left: 16px;
        }
        
        .complexity-bar {
            height: 6px;
            background: #e8e8e8;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .complexity-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }
        
        .complexity-bar-fill.o1 { background: #22c55e; width: 10%; }
        .complexity-bar-fill.ologn { background: #3b82f6; width: 30%; }
        .complexity-bar-fill.onlogn { background: #f59e0b; width: 70%; }
        
        .complexity-value {
            font-family: monospace;
            font-size: 12px;
            font-weight: 600;
            min-width: 65px;
            text-align: right;
        }
        
        /* Complexity Chart */
        .complexity-chart {
            background: #fafafa;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .chart-title {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .chart-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .chart-row {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 11px;
        }
        
        .chart-label {
            width: 100px;
            font-weight: 500;
            color: #666;
        }
        
        .chart-line {
            flex: 1;
            height: 24px;
            background: #f0f0f0;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }
        
        .chart-line-fill {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            font-size: 10px;
            font-weight: 600;
            color: white;
            border-radius: 4px;
        }
        
        /* Result Items */
        .result-item, .history-item, .doc-item {
            background: #fafafa;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }
        
        .result-item:last-child, .history-item:last-child, .doc-item:last-child {
            margin-bottom: 0;
        }
        
        .result-item:hover, .history-item:hover, .doc-item:hover {
            background: #ffffff;
            border-color: #d0d0d0;
        }
        
        .result-header, .history-header, .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .result-title, .history-query, .doc-title {
            font-weight: 600;
            font-size: 14px;
            color: #1a1a1a;
        }
        
        .result-score, .history-time, .doc-meta {
            font-size: 11px;
            color: #888888;
            font-family: monospace;
        }
        
        .copy-btn {
            background: none;
            color: #3b82f6;
            padding: 4px 8px;
            font-size: 11px;
            width: auto;
            gap: 4px;
        }
        
        .copy-btn:hover {
            background: #eef2ff;
            transform: none;
        }
        
        /* Messages */
        .message {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 13px;
            border: 1px solid;
        }
        
        .message.success {
            background: #f0fdf4;
            border-color: #22c55e;
            color: #166534;
        }
        
        .message.error {
            background: #fef2f2;
            border-color: #ef4444;
            color: #991b1b;
        }
        
        .message.warning {
            background: #fefce8;
            border-color: #eab308;
            color: #854d0e;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: #888888;
        }
        
        .empty-state i {
            font-size: 48px;
            color: #d0d0d0;
            margin-bottom: 16px;
        }
        
        /* Suggestion Buttons */
        .suggestion-btn {
            background: #f0f0f0;
            color: #444;
            padding: 6px 12px;
            font-size: 12px;
            width: auto;
            gap: 6px;
        }
        
        /* Character Count */
        .char-count {
            font-size: 11px;
            color: #888888;
            text-align: right;
            margin-top: 6px;
        }
        
        /* Hidden Toggle */
        .hidden {
            display: none;
        }
        
        .doc-preview {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e8e8e8;
            font-size: 12px;
            color: #666666;
            animation: fadeIn 200ms ease-out;
        }
        
        /* Footer */
        .footer {
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid #e8e8e8;
            text-align: center;
            font-size: 12px;
            color: #888888;
        }
        
        /* Utility Classes */
        .mt-8 { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
        .mt-16 { margin-top: 16px; }
        .mt-24 { margin-top: 24px; }
        .mt-32 { margin-top: 32px; }
        
        /* Responsive */
        @media (max-width: 900px) {
            .container { padding: 24px 16px; }
            .grid { grid-template-columns: 1fr; gap: 16px; }
            .complexity-item { flex-direction: column; align-items: flex-start; gap: 8px; }
            .complexity-bar-container { margin-left: 32px; width: 100%; }
            .chart-row { flex-wrap: wrap; }
            .chart-label { width: 80px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <h1><i class="fas fa-search" style="color: #3b82f6; margin-right: 10px;"></i>Search Engine</h1>
            <p class="subhead">Inverted index · AND queries · Term frequency ranking</p>
            
            <div class="badge-container">
                <span class="badge badge-accent"><i class="fas fa-table"></i> Hash Map</span>
                <span class="badge"><i class="fas fa-layer-group"></i> Stack</span>
                <span class="badge"><i class="fas fa-clock"></i> Queue</span>
                <span class="badge"><i class="fas fa-chart-line"></i> Heap</span>
                <span class="badge"><i class="fas fa-share-alt"></i> Graph</span>
                <span class="badge"><i class="fas fa-sort-amount-down"></i> Merge Sort</span>
                <span class="badge"><i class="fas fa-code-branch"></i> Binary Search</span>
            </div>
            
            <div class="stats-bar">
                <div class="stat-pill"><i class="fas fa-file-alt"></i> <strong><?php echo $stats['documents_count']; ?></strong> Documents</div>
                <div class="stat-pill"><i class="fas fa-tags"></i> <strong><?php echo $stats['unique_terms']; ?></strong> Unique terms</div>
                <div class="stat-pill"><i class="fas fa-hourglass-half"></i> <strong><?php echo $stats['queue_size']; ?></strong> Queue</div>
                <div class="stat-pill"><i class="fas fa-history"></i> <strong><?php echo $stats['history_size']; ?></strong> History</div>
            </div>
        </div>
        
        <!-- Message -->
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Main Grid -->
        <div class="grid">
            <!-- Search Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-search"></i>
                    <h3>Search Documents</h3>
                </div>
                <form method="POST" id="searchForm">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="query" placeholder="e.g., PHP database (Ctrl+K to focus)" value="<?php echo htmlspecialchars($currentQuery); ?>" required>
                    <button type="submit" class="mt-12"><i class="fas fa-search"></i> Search</button>
                </form>
                
                <?php if (!empty($recentQueries)): ?>
                <div class="mt-16">
                    <p style="font-size: 11px; color: #888888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Recent searches</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <?php foreach ($recentQueries as $query): ?>
                            <button type="button" class="suggestion-btn" data-query="<?php echo htmlspecialchars($query); ?>">
                                <i class="fas fa-clock"></i> <?php echo htmlspecialchars($query); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($searchResults)): ?>
                    <div class="mt-24">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <p style="font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-chart-simple"></i> Top results — heap + merge sort
                            </p>
                            <p style="font-size: 11px; color: #3b82f6;"><i class="fas fa-stopwatch"></i> <?php echo $queryTime; ?>ms</p>
                        </div>
                        <?php foreach ($searchResults as $i => $result): ?>
                            <div class="result-item">
                                <div class="result-header">
                                    <div class="result-title"><i class="fas fa-file-lines" style="color: #3b82f6; margin-right: 8px;"></i> <?php echo ($i+1); ?>. <?php echo htmlspecialchars($result['title']); ?></div>
                                    <button type="button" class="copy-btn" data-title="<?php echo htmlspecialchars($result['title']); ?>"><i class="fas fa-copy"></i> Copy</button>
                                </div>
                                <div class="result-score"><i class="fas fa-star" style="font-size: 10px;"></i> Score: <?php echo $result['score']; ?> · <i class="fas fa-hashtag"></i> ID: <?php echo $result['document_id']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search' && $currentQuery): ?>
                    <div class="empty-state mt-16">
                        <i class="fas fa-inbox"></i>
                        <p>No results for "<strong><?php echo htmlspecialchars($currentQuery); ?></strong>"</p>
                        <p class="mt-8" style="font-size: 11px;">Try different words or add more documents</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Add Document Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i>
                    <h3>Add Document</h3>
                </div>
                <form method="POST" id="addForm">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="title" placeholder="Document title" required>
                    <textarea name="content" id="content" placeholder="Document content — will be tokenized and indexed" required class="mt-12"></textarea>
                    <div class="char-count"><i class="fas fa-text-height"></i> <span id="charCount">0</span> characters</div>
                    <button type="submit" class="mt-12"><i class="fas fa-database"></i> Index Document</button>
                </form>
                <p class="mt-16" style="font-size: 11px; color: #888888;"><i class="fas fa-info-circle"></i> Documents enter FIFO queue, then added to inverted index.</p>
            </div>
            
            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i>
                    <h3>Data Structures</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $stats['unique_terms']; ?></div>
                        <div class="stat-label"><i class="fas fa-table"></i> Hash Map</div>
                        <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(100, ($stats['unique_terms'] / 100) * 100); ?>%"></div></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $stats['documents_count']; ?></div>
                        <div class="stat-label"><i class="fas fa-file"></i> Documents</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $stats['queue_size']; ?></div>
                        <div class="stat-label"><i class="fas fa-hourglass-half"></i> Queue (FIFO)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $stats['history_size']; ?></div>
                        <div class="stat-label"><i class="fas fa-layer-group"></i> Stack (LIFO)</div>
                    </div>
                </div>
            </div>
            
            <!-- Search History Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history"></i>
                    <h3>Search History — Stack (LIFO)</h3>
                </div>
                <div class="button-group">
                    <form method="POST" style="flex: 1;">
                        <input type="hidden" name="action" value="undo">
                        <button type="submit" class="secondary"><i class="fas fa-undo"></i> Pop Stack (Undo)</button>
                    </form>
                    <form method="POST" style="flex: 1;">
                        <input type="hidden" name="action" value="clear_history">
                        <button type="submit" class="danger"><i class="fas fa-trash-alt"></i> Clear History</button>
                    </form>
                </div>
                
                <div class="mt-16">
                    <?php if (empty($history)): ?>
                        <div class="empty-state" style="padding: 32px 16px;">
                            <i class="fas fa-inbox"></i>
                            <p>No search history</p>
                            <p class="mt-8" style="font-size: 11px;">Run a search — results appear here in LIFO order</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($history as $item): ?>
                            <div class="history-item">
                                <div class="history-header">
                                    <div class="history-query"><i class="fas fa-search" style="color: #3b82f6; margin-right: 8px;"></i> <?php echo htmlspecialchars($item['query']); ?></div>
                                </div>
                                <div class="history-time"><i class="far fa-calendar-alt"></i> <?php echo $item['timestamp']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Time Complexity Card with Visualization -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i>
                    <h3>Time Complexity Analysis</h3>
                </div>
                
                <!-- Detailed Complexity List with Bars -->
                <div class="complexity-container">
                    <div class="complexity-item">
                        <div class="complexity-info">
                            <i class="fas fa-table"></i>
                            <span class="complexity-name">Hash map lookup</span>
                            <span class="complexity-badge o1">O(1)</span>
                        </div>
                        <div class="complexity-bar-container">
                            <div class="complexity-bar">
                                <div class="complexity-bar-fill o1"></div>
                            </div>
                        </div>
                        <div class="complexity-value">Constant</div>
                    </div>
                    
                    <div class="complexity-item">
                        <div class="complexity-info">
                            <i class="fas fa-layer-group"></i>
                            <span class="complexity-name">Stack push/pop</span>
                            <span class="complexity-badge o1">O(1)</span>
                        </div>
                        <div class="complexity-bar-container">
                            <div class="complexity-bar">
                                <div class="complexity-bar-fill o1"></div>
                            </div>
                        </div>
                        <div class="complexity-value">Constant</div>
                    </div>
                    
                    <div class="complexity-item">
                        <div class="complexity-info">
                            <i class="fas fa-hourglass-half"></i>
                            <span class="complexity-name">Queue enqueue/dequeue</span>
                            <span class="complexity-badge o1">O(1)</span>
                        </div>
                        <div class="complexity-bar-container">
                            <div class="complexity-bar">
                                <div class="complexity-bar-fill o1"></div>
                            </div>
                        </div>
                        <div class="complexity-value">Constant</div>
                    </div>
                    
                    <div class="complexity-item">
                        <div class="complexity-info">
                            <i class="fas fa-chart-simple"></i>
                            <span class="complexity-name">Heap extract max</span>
                            <span class="complexity-badge ologin">O(log n)</span>
                        </div>
                        <div class="complexity-bar-container">
                            <div class="complexity-bar">
                                <div class="complexity-bar-fill ologin"></div>
                            </div>
                        </div>
                        <div class="complexity-value">Logarithmic</div>
                    </div>
                    
                    <div class="complexity-item">
                        <div class="complexity-info">
                            <i class="fas fa-code-branch"></i>
                            <span class="complexity-name">Binary search</span>
                            <span class="complexity-badge ologin">O(log n)</span>
                        </div>
                        <div class="complexity-bar-container">
                            <div class="complexity-bar">
                                <div class="complexity-bar-fill ologin"></div>
                            </div>
                        </div>
                        <div class="complexity-value">Logarithmic</div>
                    </div>
                    
                    <div class="complexity-item">
                        <div class="complexity-info">
                            <i class="fas fa-sort-amount-down"></i>
                            <span class="complexity-name">Merge sort</span>
                            <span class="complexity-badge onlogn">O(n log n)</span>
                        </div>
                        <div class="complexity-bar-container">
                            <div class="complexity-bar">
                                <div class="complexity-bar-fill onlogn"></div>
                            </div>
                        </div>
                        <div class="complexity-value">Linearithmic</div>
                    </div>
                </div>
                
                <!-- Visual Comparison Chart -->
                <div class="complexity-chart">
                    <div class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        <span>How Fast? (n = 1,000 items)</span>
                    </div>
                    <div class="chart-grid">
                        <div class="chart-row">
                            <div class="chart-label">O(1) — Constant</div>
                            <div class="chart-line">
                                <div class="chart-line-fill" style="width: 2%; background: #22c55e;">1 step</div>
                            </div>
                        </div>
                        <div class="chart-row">
                            <div class="chart-label">O(log n) — Logarithmic</div>
                            <div class="chart-line">
                                <div class="chart-line-fill" style="width: 5%; background: #3b82f6;">10 steps</div>
                            </div>
                        </div>
                        <div class="chart-row">
                            <div class="chart-label">O(n) — Linear</div>
                            <div class="chart-line">
                                <div class="chart-line-fill" style="width: 20%; background: #f59e0b;">1,000 steps</div>
                            </div>
                        </div>
                        <div class="chart-row">
                            <div class="chart-label">O(n log n) — Linearithmic</div>
                            <div class="chart-line">
                                <div class="chart-line-fill" style="width: 40%; background: #ef4444;">10,000 steps</div>
                            </div>
                        </div>
                        <div class="chart-row">
                            <div class="chart-label">O(n²) — Quadratic</div>
                            <div class="chart-line">
                                <div class="chart-line-fill" style="width: 100%; background: #7c3aed;">1,000,000 steps</div>
                            </div>
                        </div>
                    </div>
                    <p style="font-size: 11px; color: #888888; margin-top: 16px; text-align: center;">
                        <i class="fas fa-info-circle"></i> Your search engine uses only the fastest complexities: O(1), O(log n), and O(n log n)
                    </p>
                </div>
            </div>
            
            <!-- Recent Documents Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-folder-open"></i>
                    <h3>Recent Documents</h3>
                </div>
                <?php if (empty($documents)): ?>
                    <div class="empty-state" style="padding: 32px 16px;">
                        <i class="fas fa-folder-open"></i>
                        <p>No documents</p>
                        <p class="mt-8" style="font-size: 11px;">Use the form to add your first document</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($documents, 0, 5) as $doc): ?>
                        <div class="doc-item">
                            <div class="doc-header">
                                <div class="doc-title" style="cursor: pointer;" onclick="this.parentElement.parentElement.querySelector('.doc-preview').classList.toggle('hidden')">
                                    <i class="fas fa-file-alt" style="color: #3b82f6; margin-right: 8px;"></i> <?php echo htmlspecialchars($doc['title']); ?>
                                    <i class="fas fa-chevron-down" style="font-size: 10px; color: #3b82f6; margin-left: 8px;"></i>
                                </div>
                            </div>
                            <div class="doc-meta"><i class="fas fa-hashtag"></i> ID <?php echo $doc['id']; ?> · <i class="far fa-clock"></i> <?php echo $doc['created_at']; ?></div>
                            <div class="doc-preview hidden">
                                <?php echo htmlspecialchars(substr($doc['content'], 0, 150)); ?>...
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><i class="fas fa-code"></i> Data Structures & Algorithms Project</p>
            <p class="mt-8">Hash Map · Stack · Queue · Heap · Graph · Merge Sort · Binary Search</p>
            <p class="mt-8" style="font-size: 11px;"><i class="fas fa-keyboard"></i> Tip: Press Ctrl+K to focus search · Click on document titles to expand preview</p>
            <p class="mt-8" style="font-size: 11px;"><i class="fas fa-chart-line"></i> All operations use optimal time complexity — no slow O(n²) algorithms!</p>
        </div>
    </div>
    
    <script>
        // Character counter
        const contentArea = document.getElementById('content');
        if (contentArea) {
            contentArea.addEventListener('input', function() {
                document.getElementById('charCount').innerText = this.value.length;
            });
        }
        
        // Search suggestions
        document.querySelectorAll('.suggestion-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelector('input[name="query"]').value = this.dataset.query;
                this.closest('form').querySelector('button').click();
            });
        });
        
        // Copy buttons
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                navigator.clipboard.writeText(this.dataset.title);
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => { this.innerHTML = originalHtml; }, 1500);
            });
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.querySelector('input[name="query"]').focus();
            }
            if (e.key === 'Escape' && document.activeElement === document.querySelector('input[name="query"]')) {
                document.querySelector('input[name="query"]').value = '';
            }
        });
        
        // Chevron rotation on expand
        document.querySelectorAll('.doc-title').forEach(title => {
            title.addEventListener('click', function() {
                const icon = this.querySelector('.fa-chevron-down');
                if (icon) {
                    icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            });
        });
    </script>
</body>
</html>