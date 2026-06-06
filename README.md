# Basic Search Engine - DSA Project

## Problem Statement
A search engine that indexes documents and provides fast AND queries using an inverted index.

## Features
- ✅ Inverted index using **Hash Map** (O(1) lookup)
- ✅ Search history using **Stack** (undo functionality)
- ✅ Document indexing using **Queue** (FIFO processing)
- ✅ Top-K results using **Heap/Priority Queue**
- ✅ Word relationships using **Graph** (BFS/DFS)
- ✅ Result sorting using **Merge Sort** (O(n log n))
- ✅ Term lookup using **Binary Search** (O(log n))

## Data Structures Implementation

| Structure | Location | Usage |
|-----------|----------|-------|
| Hash Map | InvertedIndex.php | term → document IDs |
| Stack | SearchHistory.php | search history |
| Queue | IndexQueue.php | indexing jobs |
| Heap | TopResultsHeap.php | top-K results |
| Graph | WordGraph.php | word relationships |
| Sorting | ResultSorter.php | merge sort |
| Searching | TermSearcher.php | binary search |

## Architecture Diagram