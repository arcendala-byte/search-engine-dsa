<?php
// test_api.php - API for browser tests
require_once 'src/SearchEngine_MySQL.php';

header('Content-Type: application/json');

$engine = new SearchEngine();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'search':
        $query = $_GET['q'] ?? '';
        $results = $engine->search($query);
        
        $output = [
            'query' => $query,
            'count' => count($results),
            'results' => $results
        ];
        echo json_encode($output);
        break;
        
    case 'exists':
        $term = $_GET['term'] ?? '';
        $results = $engine->search($term);
        
        $output = [
            'term' => $term,
            'exists' => count($results) > 0,
            'steps' => ceil(log(max(1, $engine->getStats()['unique_terms']), 2)) . ' max'
        ];
        echo json_encode($output);
        break;
        
    default:
        echo json_encode(['error' => 'Unknown action']);
}
?>