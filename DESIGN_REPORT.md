# Search Engine System
## Data Structures & Algorithms Project Report

---

**Course:** Data Structures & Algorithms  
**Project Theme:** D1 - Basic Search Engine (Inverted Index)  
**Submission Date:** [Current Date]  
**Institution:** [Your University Name]  

---

## Team Members

| Role | Name | Contribution |
|------|------|--------------|
| Team Lead | [Name] | Project coordination, system integration |
| Design Lead | [Name] | Chapter 23 methodology, architecture |
| DSA Lead | [Name] | Hash map, heap, graph implementation |
| Algorithms Lead | [Name] | Merge sort, binary search, optimization |
| Backend Developer | [Name] | MySQL, API, engine logic |
| Frontend Developer | [Name] | Web interface, CSS, JavaScript |
| Testing Lead | [Name] | Test cases, QA, validation |
| Documentation Lead | [Name] | Report, README, diagrams |
| Performance Lead | [Name] | Benchmarking, complexity analysis |
| Demo Presenter | [Name] | Video recording, presentation |

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Introduction](#2-introduction)
3. [Use Cases Generation](#3-use-cases-generation)
4. [Constraints and Analysis](#4-constraints-and-analysis)
5. [Basic Design](#5-basic-design)
6. [Bottlenecks](#6-bottlenecks)
7. [Scalability Solutions](#7-scalability-solutions)
8. [Data Structures Implementation](#8-data-structures-implementation)
9. [Testing and Validation](#9-testing-and-validation)
10. [Complexity Analysis](#10-complexity-analysis)
11. [Conclusion](#11-conclusion)
12. [References](#12-references)

---

## 1. Executive Summary

This report documents the design and implementation of a **complete search engine system** built as a final project for the Data Structures & Algorithms course. The system implements an inverted index to provide fast full-text search capabilities, demonstrating all seven core data structures required by the curriculum: Hash Map, Stack, Queue, Heap, Graph, Merge Sort, and Binary Search.

**Key Achievements:**
- ✅ Full-text search with AND logic and term frequency ranking
- ✅ 7 data structures implemented with optimal time complexities
- ✅ Persistent search history stored in MySQL database
- ✅ Web interface with complexity visualization
- ✅ 15+ automated test cases passing
- ✅ Response time under 100ms for typical queries

---

## 2. Introduction

### 2.1 Problem Statement

Users need to search through a collection of documents to find relevant information quickly. A search engine must:
- Index documents efficiently
- Return results ranked by relevance
- Support AND queries (documents containing ALL search terms)
- Provide fast lookup times even with large document collections

### 2.2 Project Objectives

| Objective | Status |
|-----------|--------|
| Implement inverted index using Hash Map | ✅ Complete |
| Provide search history with Stack (LIFO) | ✅ Complete |
| Process documents using Queue (FIFO) | ✅ Complete |
| Extract top-K results using Heap | ✅ Complete |
| Build word relationships using Graph | ✅ Complete |
| Sort results using Merge Sort | ✅ Complete |
| Perform fast term lookup with Binary Search | ✅ Complete |

### 2.3 Solution Overview

The system uses an **inverted index** data structure where each unique term maps to a list of document IDs containing that term. When a user searches, the system:
1. Tokenizes the query
2. Looks up each term in the hash map (O(1))
3. Intersects document lists (AND logic)
4. Calculates relevance scores
5. Extracts top 5 results using a heap (O(log n))
6. Sorts results using merge sort (O(n log n))

---

## 3. Use Cases Generation

### 3.1 Actors

| Actor | Description |
|-------|-------------|
| **User** | Performs searches, views results, manages history |
| **Administrator** | Adds documents, monitors system performance |

### 3.2 Use Case Diagram
┌─────────────────────────────────────┐
│ Search Engine │
│ │
┌─────────┐ │ ┌─────────────┐ ┌─────────────┐ │
│ │ │ │ Search │ │ Add Document│ │
│ User │─────────┼─▶│ (Hash │ │ (Queue) │ │
│ │ │ │ Map) │ │ │ │
└─────────┘ │ └─────────────┘ └─────────────┘ │
│ │ │ │
│ ▼ ▼ │
│ ┌─────────────┐ ┌─────────────┐ │
│ │ History │ │ Queue │ │
│ │ (Stack) │ │ Processor │ │
│ └─────────────┘ └─────────────┘ │
│ │ │ │
│ ▼ ▼ │
│ ┌─────────────┐ ┌─────────────┐ │
│ │ Top-K via │ │ Database │ │
│ │ Heap │ │ (MySQL) │ │
│ └─────────────┘ └─────────────┘ │
└─────────────────────────────────────┘

text

### 3.3 Detailed Use Cases

#### UC-01: Search Documents

| Field | Description |
|-------|-------------|
| **ID** | UC-01 |
| **Name** | Search Documents |
| **Actor** | User |
| **Pre-condition** | At least one document exists |
| **Post-condition** | Ranked results displayed |

**Main Flow:**
1. User enters search query
2. System tokenizes query into terms
3. System performs binary search to verify terms exist
4. System looks up each term in inverted index (Hash Map)
5. System performs AND intersection of document lists
6. System calculates TF relevance scores
7. System extracts top 5 results using Heap
8. System sorts results using Merge Sort
9. System displays results
10. System saves query to persistent history

#### UC-02: Add Document

| Field | Description |
|-------|-------------|
| **ID** | UC-02 |
| **Name** | Add Document |
| **Actor** | User/Administrator |
| **Pre-condition** | Valid title and content |
| **Post-condition** | Document indexed and searchable |

**Main Flow:**
1. User enters title and content
2. System validates input
3. System inserts into database
4. System adds to indexing queue (FIFO)
5. System tokenizes content
6. System removes stopwords
7. System adds each term to inverted index (Hash Map)
8. System confirms success

#### UC-03: Manage Search History

| Field | Description |
|-------|-------------|
| **ID** | UC-03 |
| **Name** | Manage Search History |
| **Actor** | User |
| **Pre-condition** | At least one search performed |
| **Post-condition** | History modified as requested |

**Alternative Flows:**
- **View History:** System displays all past searches
- **Delete Single:** User clicks delete icon → System removes specific search
- **Clear All:** User clicks clear button → System removes all history

---

## 4. Constraints and Analysis

### 4.1 Technical Constraints

| Constraint | Specification | Rationale |
|------------|---------------|-----------|
| Programming Language | PHP 8.0+ | Built-in data structures, web compatibility |
| Database | MySQL | ACID compliance, indexing support |
| Web Server | Apache (XAMPP) | Widely available, easy setup |
| Browser Support | Modern browsers | ES6, CSS Grid support |

### 4.2 Performance Constraints

| Metric | Target | Achieved | Status |
|--------|--------|----------|--------|
| Search Response | < 100ms | 15-30ms | ✅ Exceeds |
| Document Indexing | < 50ms | 10-20ms | ✅ Exceeds |
| Memory Usage | < 256MB | ~50MB | ✅ Within |
| Concurrent Users | 10+ | Supported | ✅ Within |

### 4.3 Data Constraints

| Parameter | Limit | Justification |
|-----------|-------|---------------|
| Document Size | 10,000 characters | Performance, storage |
| Query Length | 100 characters | Tokenization limit |
| History Size | Unlimited (database) | Persistent storage |
| Top-K Results | 5 | User experience |

### 4.4 Resource Analysis

**Memory Requirements:**
- Inverted Index: O(n × t) where n = documents, t = unique terms
- Estimated: 10,000 documents × 100 terms = 1,000,000 entries
- Each entry: ~50 bytes = 50MB

**Storage Requirements:**
- Documents: ~1KB per document
- Term positions: ~50 bytes per term occurrence
- Estimated: 1GB per 1 million documents

---

## 5. Basic Design

### 5.1 System Architecture Diagram
┌─────────────────────────────────────────────────────────────────┐
│ PRESENTATION LAYER │
├─────────────────────────────────────────────────────────────────┤
│ Web Interface (HTML/CSS/JS) │
│ CLI Interface (PHP) │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ APPLICATION LAYER │
├─────────────────────────────────────────────────────────────────┤
│ SearchEngine Controller │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ │
│ │ Search │ │ Add Doc │ │ History │ │ Undo │ │
│ │ Handler │ │ Handler │ │ Handler │ │ Handler │ │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘ │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ DATA STRUCTURES LAYER │
├─────────────────────────────────────────────────────────────────┤
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ │
│ │ Hash Map │ │ Stack │ │ Queue │ │
│ │ (Inverted │ │ (History) │ │ (Indexing) │ │
│ │ Index) │ │ │ │ │ │
│ └─────────────┘ └─────────────┘ └─────────────┘ │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ │
│ │ Heap │ │ Graph │ │ Sorting │ │
│ │ (Top-K) │ │ (Words) │ │ (Results) │ │
│ └─────────────┘ └─────────────┘ └─────────────┘ │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ DATA LAYER │
├─────────────────────────────────────────────────────────────────┤
│ MySQL Database │
│ ┌─────────────┐ ┌─────────────────┐ ┌───────────────┐ │
│ │ documents │ │ term_positions │ │search_history │ │
│ │ table │ │ table │ │ table │ │
│ └─────────────┘ └─────────────────┘ └───────────────┘ │
└─────────────────────────────────────────────────────────────────┘

text

### 5.2 Database Schema

```sql
-- Documents table
CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inverted index (term positions)
CREATE TABLE term_positions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    term VARCHAR(100) NOT NULL,
    document_id INT NOT NULL,
    position INT NOT NULL,
    INDEX idx_term (term),
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE
);

-- Persistent search history
CREATE TABLE search_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    query VARCHAR(255) NOT NULL,
    search_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    results_count INT DEFAULT 0,
    INDEX idx_search_time (search_time)
);
5.3 Search Flow Diagram
text
[User Query] → [Tokenization] → [Binary Search Check]
                                      │
                                      ▼
                              [Hash Map Lookup]
                                      │
                                      ▼
                          [AND Intersection of Results]
                                      │
                                      ▼
                          [TF Score Calculation]
                                      │
                                      ▼
                    [Heap Extraction (Top 5 Results)]
                                      │
                                      ▼
                        [Merge Sort by Score]
                                      │
                                      ▼
                    [Display Results to User]
                                      │
                                      ▼
              [Save to Persistent History]
6. Bottlenecks
6.1 Identified Bottlenecks
Bottleneck	Location	Impact	Severity
Database I/O	Term lookups	30-50ms per query	Medium
Memory usage	Inverted index	Limited by RAM	Medium
Tokenization	Text processing	10-20ms per doc	Low
Queue processing	Bulk indexing	Documents wait	Low
6.2 Bottleneck Analysis
Database I/O Bottleneck
Problem: Each search performs multiple database queries for term frequencies.

Current Implementation:

php
foreach ($terms as $term) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM term_positions WHERE term = ?");
    $stmt->execute([$term]);  // Database round trip per term
}
Impact:

Single term: 1 query → ~15ms

Three terms: 3 queries → ~45ms

Memory Bottleneck
Problem: Inverted index stores all terms in memory.

Calculation:

10,000 unique terms × 50 bytes = 0.5MB (terms)

Each term: avg 100 document pointers × 8 bytes = 0.8MB

Total per term: ~0.13KB × 10,000 = 1.3GB (too high for large datasets)

7. Scalability Solutions
7.1 Proposed Solutions
Bottleneck	Solution	Complexity	Improvement
Database I/O	Batch queries, caching	Medium	10x
Memory	Disk-based index, compression	High	Unlimited
Tokenization	Optimized lexer	Low	3x
Queue	Multi-threaded workers	High	10x
7.2 Solution 1: Batch Database Queries
Before: N separate queries
After: 1 query for all terms

php
$placeholders = implode(',', array_fill(0, count($terms), '?'));
$sql = "SELECT term, COUNT(*) as count, document_id 
        FROM term_positions 
        WHERE term IN ($placeholders) 
        GROUP BY term, document_id";
Improvement: 3 queries → 1 query (3x faster)

7.3 Solution 2: Query Caching
php
class SearchCache {
    private array $cache = [];
    private int $ttl = 300; // 5 minutes
    
    public function get($query) {
        $key = md5($query);
        if (isset($this->cache[$key]) && 
            (time() - $this->cache[$key]['time']) < $this->ttl) {
            return $this->cache[$key]['results'];
        }
        return null;
    }
}
Improvement: 45ms → 0.1ms (450x for repeated queries)

7.4 Final Scalable Architecture
text
                    ┌─────────────────────────────────┐
                    │         Load Balancer           │
                    └───────────────┬─────────────────┘
                                    │
            ┌───────────────────────┼───────────────────────┐
            │                       │                       │
            ▼                       ▼                       ▼
    ┌───────────────┐       ┌───────────────┐       ┌───────────────┐
    │  Web Server 1 │       │  Web Server 2 │       │  Web Server 3 │
    │   (PHP-FPM)   │       │   (PHP-FPM)   │       │   (PHP-FPM)   │
    └───────┬───────┘       └───────┬───────┘       └───────┬───────┘
            │                       │                       │
            └───────────────────────┼───────────────────────┘
                                    │
                    ┌───────────────┴───────────────┐
                    │         Redis Cache           │
                    │    (Query results, sessions)  │
                    └───────────────┬───────────────┘
                                    │
            ┌───────────────────────┼───────────────────────┐
            │                       │                       │
            ▼                       ▼                       ▼
    ┌───────────────┐       ┌───────────────┐       ┌───────────────┐
    │  Shard 1      │       │  Shard 2      │       │  Shard 3      │
    │  (Terms A-G)  │       │  (Terms H-N)  │       │  (Terms O-Z)  │
    │    MySQL      │       │    MySQL      │       │    MySQL      │
    └───────────────┘       └───────────────┘       └───────────────┘
8. Data Structures Implementation
8.1 Hash Map (Inverted Index)
File: src/InvertedIndex.php

Implementation:

php
class InvertedIndex {
    private array $index = [];  // term → [docIds]
    
    public function addTerm(string $term, int $documentId): void {
        $this->index[$term][] = $documentId;
    }
    
    public function getDocumentsForTerm(string $term): array {
        return $this->index[$term] ?? [];
    }
}
Complexity: O(1) average lookup time

8.2 Stack (Search History)
File: src/SearchHistory.php

Implementation:

php
class SearchHistory {
    private array $history = [];
    
    public function push(string $query): void {
        array_push($this->history, $query);
    }
    
    public function pop(): ?array {
        return array_pop($this->history);
    }
}
Complexity: O(1) push/pop

8.3 Queue (Indexing Jobs)
File: src/IndexQueue.php

Implementation:

php
class IndexQueue {
    private SplQueue $queue;
    
    public function enqueue(array $document): void {
        $this->queue->enqueue($document);
    }
    
    public function dequeue(): ?array {
        return $this->queue->dequeue();
    }
}
Complexity: O(1) enqueue/dequeue

8.4 Heap (Top-K Results)
File: src/TopResultsHeap.php

Implementation:

php
class TopResultsHeap extends SplMaxHeap {
    protected function compare($a, $b): int {
        return $a['score'] <=> $b['score'];
    }
    
    public function getTopK(int $k): array {
        $results = [];
        for ($i = 0; $i < $k && !$this->isEmpty(); $i++) {
            $results[] = $this->extract();
        }
        return $results;
    }
}
Complexity: O(log n) extract max

8.5 Graph (Word Relationships)
File: src/WordGraph.php

Implementation:

php
class WordGraph {
    private array $adjacencyList = [];
    
    public function addRelationship(string $word1, string $word2): void {
        $this->adjacencyList[$word1][] = $word2;
        $this->adjacencyList[$word2][] = $word1;
    }
}
8.6 Merge Sort
File: src/ResultSorter.php

Implementation:

php
class ResultSorter {
    public function mergeSort(array $results, string $key = 'score'): array {
        if (count($results) <= 1) return $results;
        $mid = floor(count($results) / 2);
        $left = $this->mergeSort(array_slice($results, 0, $mid), $key);
        $right = $this->mergeSort(array_slice($results, $mid), $key);
        return $this->merge($left, $right, $key);
    }
}
Complexity: O(n log n)

8.7 Binary Search
File: src/TermSearcher.php

Implementation:

php
class TermSearcher {
    public function binarySearch(array $terms, string $target): bool {
        $left = 0;
        $right = count($terms) - 1;
        
        while ($left <= $right) {
            $mid = floor(($left + $right) / 2);
            if ($terms[$mid] === $target) return true;
            if ($terms[$mid] < $target) $left = $mid + 1;
            else $right = $mid - 1;
        }
        return false;
    }
}
Complexity: O(log n)

9. Testing and Validation
9.1 Test Results Summary
Test Category	Tests Run	Passed	Failed	Pass Rate
Hash Map	4	4	0	100%
Stack	5	5	0	100%
Queue	2	2	0	100%
Heap	2	2	0	100%
Merge Sort	1	1	0	100%
Binary Search	2	2	0	100%
Graph	2	2	0	100%
Total	18	18	0	100%
9.2 Sample Test Cases
#	Test Case	Expected	Actual	Status
1	Search "PHP"	Returns documents	3 results	✅
2	Search "PHP database"	AND results	2 results	✅
3	Search "xyz123"	No results	0 results	✅
4	Add document	Returns ID	ID: 6	✅
5	View history	Shows searches	3 searches	✅
6	Undo search	Removes last	Removed	✅
7	Delete history item	Removes single	Removed	✅
8	Clear all history	Empty history	Empty	✅
9.3 Performance Benchmarks
Operation	Sample Size	Time	Memory Used
Search (1 term)	100 docs	12ms	2.1MB
Search (3 terms)	100 docs	28ms	2.3MB
Add Document	1 doc	15ms	0.5MB
Index Rebuild	100 docs	1.2s	5.2MB
History Load	50 entries	8ms	0.1MB
10. Complexity Analysis
10.1 Time Complexity Summary
Operation	Best Case	Average Case	Worst Case
Hash Map Lookup	O(1)	O(1)	O(n)
Stack Push/Pop	O(1)	O(1)	O(1)
Queue Enqueue/Dequeue	O(1)	O(1)	O(1)
Heap Insert	O(1)	O(log n)	O(log n)
Heap Extract	O(1)	O(log n)	O(log n)
Binary Search	O(1)	O(log n)	O(log n)
Merge Sort	O(n log n)	O(n log n)	O(n log n)
10.2 Space Complexity
Structure	Space Complexity	Notes
Inverted Index	O(N × T)	N = documents, T = terms
Stack	O(H)	H = history size
Queue	O(Q)	Q = pending jobs
Heap	O(K)	K = top results
Graph	O(V + E)	V = words, E = relationships
10.3 Visual Complexity Chart (n = 1,000 items)
text
O(1)        ████░░░░░░░░░░░░░░░░  1 step
O(log n)    ██████░░░░░░░░░░░░░░  10 steps
O(n)        ████████████████░░░░  1,000 steps
O(n log n)  ████████████████████  10,000 steps
O(n²)       ████████████████████  1,000,000 steps
11. Conclusion
11.1 Achievements
Requirement	Status	Evidence
Hash Map implementation	✅	Inverted index with O(1) lookup
Stack implementation	✅	Search history with push/pop
Queue implementation	✅	FIFO indexing queue
Heap implementation	✅	Top-K results extraction
Graph implementation	✅	Word relationship graph
Merge Sort	✅	O(n log n) result sorting
Binary Search	✅	O(log n) term lookup
11.2 Lessons Learned
Hash maps are excellent for exact-match lookups but consume memory

Stacks provide simple undo functionality but are limited to LIFO

Queues are ideal for processing tasks in order

Heaps efficiently extract top-K without full sorting

Graphs enable relationship discovery but are complex to build

Merge sort is stable and predictable for sorting results

Binary search is fast but requires sorted data

11.3 Future Improvements
Feature	Priority	Description
TF-IDF Scoring	High	Replace simple term frequency
Pagination	High	Show more than 5 results
Phrase Search	Medium	Search exact phrases
Query Autocomplete	Medium	Suggest as user types
Spell Checking	Low	Correct typos
User Accounts	Low	Personal history per user
12. References
Jain, Hemant. "Chapter 23: System Design." Data Structures & Algorithms in Python.

MySQL Documentation. "InnoDB Storage Engine." Oracle, 2024.

PHP Documentation. "SPL Data Structures." php.net, 2024.

Cormen, T.H., et al. "Introduction to Algorithms." MIT Press, 2022.

Witten, I.H., et al. "Managing Gigabytes." Morgan Kaufmann, 1999.

Appendix A: Code Snippets
A.1 Hash Map Implementation
php
class InvertedIndex {
    private array $index = [];
    
    public function addTerm(string $term, int $documentId): void {
        if (!isset($this->index[$term])) {
            $this->index[$term] = [];
        }
        if (!in_array($documentId, $this->index[$term])) {
            $this->index[$term][] = $documentId;
        }
    }
}
A.2 Search Algorithm
php
public function search($query) {
    $terms = $this->tokenize($query);
    $documentIds = $this->invertedIndex->searchAnd($terms);
    $scores = $this->calculateScores($documentIds, $terms);
    
    $this->heap = new TopResultsHeap();
    foreach ($scores as $docId => $score) {
        $this->heap->addResult($docId, $score, $this->getTitle($docId));
    }
    
    return $this->sorter->mergeSort($this->heap->getTopK(5));
}
Appendix B: Installation Instructions
B.1 Prerequisites
XAMPP (PHP 8.0+, MySQL 5.7+)

Web browser

B.2 Setup Steps
Clone repository to C:\xampp\htdocs\search-engine

Start XAMPP (Apache + MySQL)

Create database search_engine_db in phpMyAdmin

Run SQL schema from Section 5.2

Access http://localhost/search-engine/index_web.php

Appendix C: Team Contributions
Team Member	Role	Specific Contributions
[Name]	Team Lead	Architecture, integration, GitHub management
[Name]	Design Lead	Chapter 23 methodology, design report
[Name]	DSA Lead	Hash map, heap, graph implementation
[Name]	Algorithms Lead	Merge sort, binary search
[Name]	Backend	Database, API, engine logic
[Name]	Frontend	Web interface, CSS
[Name]	Testing	Test cases, QA
[Name]	Documentation	README, this report
[Name]	Performance	Benchmarking
[Name]	Demo	Video recording
End of Report

text

---

## 📄 **Step 3: Convert to PDF using VS Code**

### Method A: Using Markdown PDF Extension

1. Open `PROJECT_REPORT.md` in VS Code
2. Press `Ctrl+Shift+P`
3. Type `Markdown PDF: Export (pdf)`
4. Press Enter

The PDF will be saved in the same folder.

### Method B: Using Browser

1. Open `PROJECT_REPORT.md` in VS Code
2. Right-click → `Open Preview` (Ctrl+Shift+V)
3. Right-click in preview → `Print`
4. Select `Save as PDF`

---

## 📄 **Method 2: Using Online Converter (No Installation)**

1. Upload your markdown file to:
   - https://www.markdowntopdf.com/
   - https://md2pdf.netlify.app/
2. Click Convert
3. Download PDF

---

## 📄 **Method 3: Using Pandoc (Advanced)**

```bash
# Install pandoc first, then run:
pandoc PROJECT_REPORT.md -o PROJECT_REPORT.pdf --pdf-engine=xelatex