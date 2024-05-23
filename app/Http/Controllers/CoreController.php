<?php

namespace App\Http\Controllers;

use App\Models\Core;
use Illuminate\Http\Request;

class CoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('core.index');
    }

    public function process(Request $request) {
        $file = $request->file('csv_file');
        $capacity = $request->input('capacity');
        list($weights, $values, $labels) = $this->readCsvFile($file->getRealPath());

        $resultGD = $this->knapsackGD($weights, $values, $labels, $capacity);
        $resultDP = $this->knapsackDP($weights, $values, $capacity);

        return view('core.result', compact('resultDP', 'resultGD', 'weights', 'values', 'labels'));
    }

    private function readCsvFile($filepath)
    {
        $weights = []; // weight of the fruit
        $values = []; // value of each fruit
        $labels = []; // label of the fruit
        $sizes = [];
        if (($handle = fopen($filepath, 'r')) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            $weightIndex = array_search('Weight (g)', $header);
            $sizeIndex = array_search('Size (cm)', $header);
            $blemishesIndex = array_search('Blemishes (Y/N)', $header);
            $labelIndex = array_search('labels', $header);

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $weight = $data[$weightIndex]; // convert to float
                $size = $data[$sizeIndex]; // convert to float
                $blemishes = $data[$blemishesIndex];
                $label = $data[$labelIndex];

                // Initialize value with size divided by weight
                $value = $weight / $size;

                // // If there are no blemishes, add 1 to the value
                // if (strtoupper(substr($blemishes, 0, 1)) == 'N') {
                //     $value += 1;
                // }

                // Add the calculated values to arrays
                $weights[] = $weight;
                $values[] = (float)$value;
                $sizes[] = $size;
                $labels[] = $label;
            }
            fclose($handle);
            //dd($values);
        }
        return [$weights, $values, $labels];
    }

    public function knapsackGD($weights, $values, $labels, $capacity) {
        // Combine weight, value, and label into single items array
        $items = array_map(null, $weights, $values, $labels);

        // Sort items by weight in ascending order
        usort($items, function($a, $b) {
            return $a[0] <=> $b[0];
        });

        $totalWeight = 0.0;
        $totalValue = 0.0;
        $itemsIncluded = [];

        foreach ($items as $item) {
            if ($totalWeight + $item[0] <= $capacity) {
                $itemsIncluded[] = $item;
                $totalWeight += $item[0];
                $totalValue += $item[1];
            }
        }

        return [
            'items' => $itemsIncluded,
            'total_value' => $totalValue,
            'total_weight' => $totalWeight
        ];
    }

    // private function knapsackDP(array $weights, array $values, int $capacity): array
    // {
    //     $n = count($weights); // number of items

    //     // Create a table to store maximum achievable values and a table to track included items
    //     $dp = array_fill(0, $n + 1, array_fill(0, $capacity + 1, 0));
    //     $included = array_fill(0, $n + 1, array_fill(0, $capacity + 1, false));

    //     // Fill the DP table for all capacities and items
    //     for ($i = 1; $i <= $n; $i++) {
    //         for ($w = 1; $w <= $capacity; $w++) {
    //             if ($weights[$i - 1] > $w) {
    //                 // If item weight is greater than capacity, inherit from previous item
    //                 $dp[$i][$w] = $dp[$i - 1][$w];
    //             } else {
    //                 // Choose the maximum value between including or excluding the current item
    //                 $excludeValue = $dp[$i - 1][$w];
    //                 $includeValue = $values[$i - 1] + $dp[$i - 1][$w - $weights[$i - 1]];
    //                 if ($includeValue > $excludeValue) {
    //                     $dp[$i][$w] = $includeValue;
    //                     $included[$i][$w] = true; // Mark item as included
    //                 } else {
    //                     $dp[$i][$w] = $excludeValue;
    //                 }
    //             }
    //         }
    //     }

    //     // Calculate the total weight based on included items
    //     $totalWeight = 0;
    //     for ($i = 0; $i < $n; $i++) {
    //         if ($included[$i + 1][$capacity]) {
    //             $totalWeight += $weights[$i];
    //         }
    //     }

    //     // Find the included items using backtracking
    //     $includedItems = $this->backtrackDP($weights, $included, $capacity, $n);

    //     return [
    //         'items' => $includedItems,
    //         'total_value' => $dp[$n][$capacity],
    //         'total_weight' => $totalWeight,
    //     ];
    // }

    // private function backtrackDP(array $weights, array $included, int $capacity, int $currentIndex): array
    // {
    //     $includedItems = [];
    //     if ($currentIndex === 0 || $capacity === 0) {
    //         return $includedItems;
    //     }

    //     // If the current item is included, add it and backtrack for the remaining capacity
    //     if ($included[$currentIndex][$capacity]) {
    //         $includedItems[] = $currentIndex - 1;
    //         return array_merge($includedItems, $this->backtrackDP($weights, $included, $capacity - $weights[$currentIndex - 1], $currentIndex - 1));
    //     } else {
    //         // If the current item is excluded, backtrack without it
    //         return $this->backtrackDP($weights, $included, $capacity, $currentIndex - 1);
    //     }
    // }

    private function knapsackDP(array $weights, array $values, int $capacity): array
    {
        $n = count($weights); // number of items

        // Create a table to store maximum achievable values and a table to track included items
        $dp = array_fill(0, $n + 1, array_fill(0, $capacity + 1, 0));
        $included = array_fill(0, $n + 1, array_fill(0, $capacity + 1, false));

        // Fill the DP table for all capacities and items
        for ($i = 1; $i <= $n; $i++) {
            for ($w = 1; $w <= $capacity; $w++) {
                if ($weights[$i - 1] > $w) {
                    // If item weight is greater than capacity, inherit from previous item
                    $dp[$i][$w] = $dp[$i - 1][$w];
                } else {
                    // Choose the maximum value between including or excluding the current item
                    $excludeValue = $dp[$i - 1][$w];
                    $includeValue = $values[$i - 1] + $dp[$i - 1][$w - $weights[$i - 1]];
                    if ($includeValue > $excludeValue) {
                        $dp[$i][$w] = $includeValue;
                        $included[$i][$w] = true; // Mark item as included
                    } else {
                        $dp[$i][$w] = $excludeValue;
                    }
                }
            }
        }

        // Calculate the total weight based on included items during backtracking
        $totalWeight = 0;
        $includedItems = $this->backtrackDP($weights, $included, $capacity, $n, $totalWeight);

        return [
            'items' => $includedItems,
            'total_value' => $dp[$n][$capacity],
            'total_weight' => $totalWeight,
        ];
    }

    private function backtrackDP(array $weights, array $included, int $capacity, int $currentIndex, int &$totalWeight): array
    {
        $includedItems = [];
        if ($currentIndex === 0 || $capacity === 0) {
            return $includedItems;
        }

        // If the current item is included, update total weight and backtrack
        if ($included[$currentIndex][$capacity]) {
            $totalWeight += $weights[$currentIndex - 1];
            $includedItems[] = $currentIndex - 1;
            return array_merge($includedItems, $this->backtrackDP($weights, $included, $capacity - $weights[$currentIndex - 1], $currentIndex - 1, $totalWeight));
        } else {
            // If the current item is excluded, backtrack without it
            return $this->backtrackDP($weights, $included, $capacity, $currentIndex - 1, $totalWeight);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Core $core)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Core $core)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Core $core)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Core $core)
    {
        //
    }
}
