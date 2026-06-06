<?php
// src/TermSearcher.php
class TermSearcher {
    
    // Binary Search - O(log n)
    public function binarySearch(array $terms, string $target): bool {
        $left = 0;
        $right = count($terms) - 1;
        
        while ($left <= $right) {
            $mid = floor(($left + $right) / 2);
            
            if ($terms[$mid] === $target) {
                return true;
            }
            
            if ($terms[$mid] < $target) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }
        
        return false;
    }
    
    // Quick sort to prepare terms for binary search
    public function quickSort(array $terms): array {
        if (count($terms) <= 1) {
            return $terms;
        }
        
        $pivot = $terms[0];
        $left = $right = [];
        
        for ($i = 1; $i < count($terms); $i++) {
            if ($terms[$i] < $pivot) {
                $left[] = $terms[$i];
            } else {
                $right[] = $terms[$i];
            }
        }
        
        return array_merge($this->quickSort($left), [$pivot], $this->quickSort($right));
    }
}