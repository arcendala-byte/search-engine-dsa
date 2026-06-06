\# Complexity Analysis



\## Data Structures



| Structure | Operation | Time Complexity | Space |

|-----------|-----------|-----------------|-------|

| Hash Map | Lookup | O(1) average | O(n) |

| Stack | Push/Pop | O(1) | O(n) |

| Queue | Enqueue/Dequeue | O(1) | O(n) |

| Heap | Insert/Extract | O(log n) | O(n) |

| Merge Sort | Sort | O(n log n) | O(n) |

| Binary Search | Search | O(log n) | O(1) |



\## Search Query Breakdown



1\. Tokenization: O(m) where m = query length

2\. Binary search check: O(log t) where t = unique terms

3\. Hash map lookup: O(1) per term

4\. Score calculation: O(d × t) where d = matching docs

5\. Heap extraction: O(k log n) where k = top results

6\. Merge sort: O(k log k)



Total: O(m + log t + d × t + k log n + k log k)



cd C:\xampp2\htdocs\search-engine
C:\xampp2\php\php.exe index.php