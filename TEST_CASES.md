\# Test Cases (15+)



| # | Test Case | Expected | Actual | Status |

|---|-----------|----------|--------|--------|

| 1 | Add document "Test" | Returns ID | ID: 6 | ✅ |

| 2 | Search "PHP" | Returns results | 1 result | ✅ |

| 3 | Search "xyz123" | Empty results | Empty | ✅ |

| 4 | AND search "PHP database" | Doc with both | Doc 1,3 | ✅ |

| 5 | View history (stack) | Shows searches | Shows 3 | ✅ |

| 6 | Undo last search | Removes from stack | Removed | ✅ |

| 7 | Add document with empty title | Error message | Shows error | ✅ |

| 8 | Search empty query | No results | No results | ✅ |

| 9 | Queue status after add | Shows 1 pending | Shows 1 | ✅ |

| 10 | Multiple searches | History grows | Size increases | ✅ |

| 11 | Clear history | Empty stack | Empty | ✅ |

| 12 | Binary search lookup | O(log n) | Works | ✅ |

| 13 | Heap top-K | Returns top 5 | Top 5 | ✅ |

| 14 | Merge sort | Sorted by score | Correct order | ✅ |

| 15 | Hash map lookup | O(1) time | Instant | ✅ |

