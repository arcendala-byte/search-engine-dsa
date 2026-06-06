<?php
// src/ResultSorter.php
class ResultSorter {
    
    // Merge Sort implementation - O(n log n)
    public function mergeSort(array $results, string $key = 'score'): array {
        if (count($results) <= 1) {
            return $results;
        }
        
        $mid = floor(count($results) / 2);
        $left = $this->mergeSort(array_slice($results, 0, $mid), $key);
        $right = $this->mergeSort(array_slice($results, $mid), $key);
        
        return $this->merge($left, $right, $key);
    }
    
    private function merge(array $left, array $right, string $key): array {
        $result = [];
        $i = $j = 0;
        
        while ($i < count($left) && $j < count($right)) {
            if ($left[$i][$key] >= $right[$j][$key]) {
                $result[] = $left[$i];
                $i++;
            } else {
                $result[] = $right[$j];
                $j++;
            }
        }
        
        while ($i < count($left)) {
            $result[] = $left[$i];
            $i++;
        }
        
        while ($j < count($right)) {
            $result[] = $right[$j];
            $j++;
        }
        
        return $result;
    }
}