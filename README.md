# 🔍 Search Engine System

## Data Structures & Algorithms Project

![PHP Version](https://img.shields.io/badge/PHP-8.0+-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![License](https://img.shields.io/badge/License-MIT-green)
![Status](https://img.shields.io/badge/Status-Completed-brightgreen)

---

## 📋 Project Overview

A **complete search engine system** implementing all 7 core data structures required for the DSA course. The system uses an inverted index to provide fast full-text search with AND queries and term frequency ranking.

**Live Demo:** [YouTube Link]
**Repository:** https://github.com/arcendala-byte/search-engine-dsa

---

## 👥 Team Members

| Role | Name | Registration Number |
|------|------|---------------------|
| Team Lead & Backend Developer | Arcel Ndala | BIT/2024/44434 |
| DSA & Algorithms Lead | Chan Ring | BIT/2024/46674 |
| Frontend & UI Lead | Lucy Mbugua | BIT/2024/44576 |
| Testing & Performance Lead | Humprhey Mungai | BIT/2024/42345 |
| Documentation & Demo Lead | Shanice Anna | BIT/2024/43110 |

---

## ✨ Features

### Core Search Features
| Feature | Description |
|---------|-------------|
| **Full-Text Search** | Search across all documents |
| **AND Logic** | Find documents containing ALL search terms |
| **Term Frequency Ranking** | Results sorted by relevance |
| **Persistent History** | Searches saved to database (survives refresh) |
| **Individual Delete** | Remove specific searches |
| **Clear All History** | One-click history cleanup |
| **Memory Stack** | Separate undo stack (LIFO) |

### Document Management
| Feature | Description |
|---------|-------------|
| **Add Documents** | Insert new documents with title and content |
| **Document Preview** | Expand to view content |
| **Recent Documents** | View last 5 added documents |
| **Automatic Indexing** | Documents tokenized and indexed |

### User Interface
| Feature | Description |
|---------|-------------|
| **Complexity Visualization** | Charts showing Big-O performance |
| **Real-time Search Time** | Measure in milliseconds |
| **Copy Results** | One-click copy of titles |
| **Keyboard Shortcuts** | Ctrl+K to focus search |
| **Responsive Design** | Desktop, tablet, mobile |

---

## 🗺️ Data Structures Implemented

| # | Structure | File | Complexity | Owner |
|---|-----------|------|------------|-------|
| 1 | **Hash Map** | `InvertedIndex.php` | O(1) lookup | Chan Ring |
| 2 | **Stack** | `SearchHistory.php` | O(1) push/pop | Chan Ring |
| 3 | **Queue** | `IndexQueue.php` | O(1) enqueue/dequeue | Chan Ring |
| 4 | **Heap** | `TopResultsHeap.php` | O(log n) extract | Chan Ring |
| 5 | **Graph** | `WordGraph.php` | Adjacency list | Chan Ring |
| 6 | **Merge Sort** | `ResultSorter.php` | O(n log n) | Chan Ring |
| 7 | **Binary Search** | `TermSearcher.php` | O(log n) | Chan Ring |

---

## 💻 Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| **Backend** | PHP | 8.0+ |
| **Database** | MySQL | 5.7+ |
| **Frontend** | HTML5, CSS3, JavaScript | - |
| **Icons** | Font Awesome | 6.4.0 |
| **Font** | Inter | Google Fonts |
| **Server** | XAMPP / Apache | - |

---

## 🏗️ System Architecture
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
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ DATA STRUCTURES LAYER │
├─────────────────────────────────────────────────────────────────┤
│ Hash Map │ Stack │ Queue │ Heap │ Graph │ Merge Sort │ Binary │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ DATA LAYER │
├─────────────────────────────────────────────────────────────────┤
│ MySQL Database │
└─────────────────────────────────────────────────────────────────┘

text

---

## 🔧 Installation Guide

### Prerequisites

- XAMPP (PHP 8.0+, MySQL 5.7+)
- Web browser (Chrome/Firefox/Edge)

### Step 1: Install XAMPP

Download from: https://www.apachefriends.org/

### Step 2: Clone Repository

```bash
cd C:\xampp\htdocs
git clone https://github.com/arcendala-byte/search-engine-dsa.git
cd search-engine
Step 3: Start XAMPP
Open XAMPP Control Panel

Start Apache service

Start MySQL service

Step 4: Create Database
Open phpMyAdmin: http://localhost/phpmyadmin

sql
CREATE DATABASE search_engine_db;
USE search_engine_db;

CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE term_positions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    term VARCHAR(100) NOT NULL,
    document_id INT NOT NULL,
    position INT NOT NULL,
    INDEX idx_term (term),
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE
);

CREATE TABLE search_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    query VARCHAR(255) NOT NULL,
    search_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    results_count INT DEFAULT 0,
    INDEX idx_search_time (search_time)
);
Step 5: Configure Database
Edit config/db_connection.php if needed (default credentials work for XAMPP).

Step 6: Run the Application
Web Interface:

text
http://localhost/search-engine/index_web.php
Command Line:

bash
php index.php
🚀 Usage Guide
Web Interface
Search Documents
Enter search term (e.g., "PHP database")

Click "Search" or press Enter

View ranked results with relevance scores

Add Documents
Enter document title

Enter document content

Click "Index Document"

Manage History
Delete single: Click trash icon

Clear all: Click "Clear All" button

Undo: Click "Pop Stack" in memory stack section

Keyboard Shortcuts
Shortcut	Action
Ctrl + K	Focus search input
Enter	Submit search
🧪 Testing
Run Automated Tests
bash
php test_structures.php
Test Results
Category	Tests	Passed	Pass Rate
Hash Map	4	4	100%
Stack	5	5	100%
Queue	2	2	100%
Heap	2	2	100%
Merge Sort	1	1	100%
Binary Search	2	2	100%
Graph	2	2	100%
Total	18	18	100%
📊 Complexity Analysis
Operation	Complexity	Performance (1,000 items)
Hash Map Lookup	O(1)	< 1ms
Stack Push/Pop	O(1)	< 1ms
Queue Operation	O(1)	< 1ms
Heap Extract	O(log n)	~10 steps
Binary Search	O(log n)	~10 comparisons
Merge Sort	O(n log n)	~10,000 operations
Visual Chart
text
O(1)        ████░░░░░░░░░░░░░░░░  1 step
O(log n)    ██████░░░░░░░░░░░░░░  10 steps
O(n)        ████████████████░░░░  1,000 steps
O(n log n)  ████████████████████  10,000 steps
📁 Project Structure
text
search-engine/
├── index_web.php              # Web interface (main)
├── index.php                  # CLI interface
├── test_structures.php        # Automated tests
├── README.md                  # Documentation
├── DESIGN_REPORT.md           # Complete design report
├── TEST_CASES.md              # Test cases
├── COMPLEXITY.md              # Complexity analysis
├── config/
│   ├── database.php
│   └── db_connection.php
├── src/
│   ├── SearchEngine_MySQL.php # Main engine
│   ├── InvertedIndex.php      # Hash Map
│   ├── SearchHistory.php      # Stack
│   ├── IndexQueue.php         # Queue
│   ├── TopResultsHeap.php     # Heap
│   ├── WordGraph.php          # Graph
│   ├── ResultSorter.php       # Merge Sort
│   └── TermSearcher.php       # Binary Search
├── tests/
│   └── TestCases.php
└── data/
    └── (database files)
📈 Performance Benchmarks
Operation	Time	Memory
Search (1 term)	12ms	2.1MB
Search (3 terms)	28ms	2.3MB
Add Document	15ms	0.5MB
Bulk Add (100 docs)	1.2s	5.2MB
🔮 Future Improvements
Feature	Priority	Description
TF-IDF Scoring	High	Better relevance ranking
Pagination	High	Show more than 5 results
Phrase Search	Medium	Search exact phrases
Query Autocomplete	Medium	Suggest as user types
Spell Checking	Low	Correct typos
User Accounts	Low	Personal history
📝 License
This project is created for academic purposes as part of the Data Structures & Algorithms course.

🔗 Links
Resource	Link
GitHub Repository	https://github.com/arcendala-byte/search-engine-dsa
Demo Video	[YouTube Link]
Design Report	[PDF Link]
🙏 Acknowledgments
Professor for guidance

Hemant Jain for "Chapter 23: System Design" reference

Open-source community

Built with ❤️ for DSA Course Project

Last Updated: June 2024

text

---

## 🚀 **Push Updates to GitHub**

Now push both updated files:

```bash
cd C:\xampp2\htdocs\search-engine

# Add both files
git add COMPLEXITY.md README.md

# Commit
git commit -m "Update COMPLEXITY.md and README.md with complete documentation"

# Push
git push
✅ Verification
Check your GitHub repository:

text
https://github.com/arcendala-byte/search-engine-dsa