<?php

namespace App\Http\Controllers;

use App\Models\Core;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        list($weights, $values, $labels, $totalItems) = $this->readCsvFile($file->getRealPath());

        $resultGDW = $this->knapsackGD_byWeight($weights, $values, $labels, $capacity);
        $resultGDV = $this->knapsackGD_byValue($weights, $values, $labels, $capacity);
        $resultGDD = $this->knapsackGD_byDensity($weights, $values, $labels, $capacity);
        $resultDP = $this->knapsackDP($weights, $values, $labels, $capacity);

        $remainingItems = $this->getRemainingItems($weights, $values, $labels, $resultDP);
        //dd($resultGDW);

        return view('core.result', compact('resultDP', 'resultGDW', 'resultGDV', 'resultGDD', 'weights', 'values', 'labels', 'remainingItems', 'totalItems', 'capacity'));
    }

    private function readCsvFile($filepath)
    {
        $weights = []; // weight of the fruit
        $values = []; // value of each fruit
        $labels = []; // label of the fruit
        $sizes = [];
        $totalItems = 0;
        if (($handle = fopen($filepath, 'r')) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            $weightIndex = array_search('Weight (g)', $header);
            $sizeIndex = array_search('Size (cm)', $header);
            $qualityIndex = array_search('Quality (1-5)', $header);
            $labelIndex = array_search('labels', $header);
            $blemishesIndex = array_search('Blemishes (Y/N)', $header);
            $valuesIndex = array_search('value', $header);

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $weight = $data[$weightIndex]; // convert to float
                $size = $data[$sizeIndex]; // convert to float
                $quality = $data[$qualityIndex];
                $label = $data[$labelIndex];
                $blemishes = $data[$blemishesIndex];

                $value = 0;

                if($valuesIndex != null) {
                    $value = $data[$valuesIndex];
                } else {
                    $value = $quality;
                    if (substr($blemishes, 0, 1) == 'N') {
                        $value += 1.0;
                    }
                }

                $weights[] = $weight;
                $values[] = (float)$value;
                $sizes[] = $size;
                $labels[] = $label;

                $totalItems++;
            }
            fclose($handle);
            //dd($values);
        }
        return [$weights, $values, $labels, $totalItems];
    }

    public function knapsackGD_byWeight($weights, $values, $labels, $capacity) {
        $items = array_map(null, $weights, $values, $labels);

        usort($items, function($a, $b) {
            return $a[0] <=> $b[0];
        });

        $totalWeight = 0.0;
        $totalValue = 0.0;
        $itemsIncluded = [];
        $totalItems = 0;

        foreach ($items as $item) {
            if ($totalWeight + $item[0] <= $capacity) {
                $itemsIncluded[] = $item;
                $totalWeight += $item[0];
                $totalValue += $item[1];
                $totalItems++;
            }
        }

        return [
            'items' => $itemsIncluded,
            'total_value' => $totalValue,
            'total_weight' => $totalWeight,
            'total_items' => $totalItems
        ];
    }

    public function knapsackGD_byValue($weights, $values, $labels, $capacity) {
        $items = array_map(null, $weights, $values, $labels);

        usort($items, function($a, $b) {
            return $b[1] <=> $a[1];
        });

        $totalWeight = 0.0;
        $totalValue = 0.0;
        $itemsIncluded = [];
        $totalItems = 0;

        foreach ($items as $item) {
            if ($totalWeight + $item[0] <= $capacity) {
                $itemsIncluded[] = $item;
                $totalWeight += $item[0];
                $totalValue += $item[1];
                $totalItems++;
            }
        }

        return [
            'items' => $itemsIncluded,
            'total_value' => $totalValue,
            'total_weight' => $totalWeight,
            'total_items' => $totalItems
        ];
    }

    public function knapsackGD_byDensity($weights, $values, $labels, $capacity) {
        $items = array_map(null, $weights, $values, $labels);

        usort($items, function($a, $b) {
            return ($b[1] / $b[0]) <=> ($a[1] / $a[0]);
        });

        $totalWeight = 0.0;
        $totalValue = 0.0;
        $itemsIncluded = [];
        $totalItems = 0;

        foreach ($items as $item) {
            if ($totalWeight + $item[0] <= $capacity) {
                $itemsIncluded[] = $item;
                $totalWeight += $item[0];
                $totalValue += $item[1];
                $totalItems++;
            }
        }

        return [
            'items' => $itemsIncluded,
            'total_value' => $totalValue,
            'total_weight' => $totalWeight,
            'total_items' => $totalItems
        ];
    }

    public function knapsackDP(array $weights, array $values, array $labels, int $capacity): array
    {
        $n = count($weights); // number of items

        $dp = array_fill(0, $n + 1, array_fill(0, $capacity + 1, 0));
        $included = array_fill(0, $n + 1, array_fill(0, $capacity + 1, false));

        for ($i = 1; $i <= $n; $i++) {
            for ($w = 1; $w <= $capacity; $w++) {
                if ($weights[$i - 1] > $w) {
                    $dp[$i][$w] = $dp[$i - 1][$w];
                } else {
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

        $totalWeight = 0;
        $includedItems = $this->backtrackDP($weights, $included, $values, $labels, $capacity, $n, $totalWeight);
        //dd($includedItems);

        return [
            'items' => $includedItems,
            'total_value' => $dp[$n][$capacity],
            'total_weight' => $totalWeight,
            'total_items' => count($includedItems)
        ];
    }

    public function backtrackDP(array $weights, array $included, array $values, array $labels, int $capacity, int $currentIndex, int &$totalWeight): array
    {
        $includedItems = [];
        if ($currentIndex === 0 || $capacity === 0) {
            return $includedItems;
        }

        if ($included[$currentIndex][$capacity]) {
            $totalWeight += $weights[$currentIndex - 1];
            $includedItems[] = [
                'index' => $currentIndex - 1,
                'Weight (g)' => $weights[$currentIndex - 1],
                'labels' => $labels[$currentIndex - 1], // Add labels
                'value' => $values[$currentIndex - 1], // Add quality
            ];
            return array_merge($includedItems, $this->backtrackDP($weights, $included, $values, $labels, $capacity - $weights[$currentIndex - 1], $currentIndex - 1, $totalWeight));
        } else {
            return $this->backtrackDP($weights, $included, $values, $labels, $capacity, $currentIndex - 1, $totalWeight);
        }
    }

    public function getRemainingItems($weights, $values, $labels, $resultDP) {
        $itemsArray = $resultDP['items'];

        $remainingItems = [];
        foreach ($labels as $index => $label) {
            $found = false;
            foreach ($itemsArray as $item) {
                if ($item['index'] === $index) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $remainingItems[] = [
                    'Weight (g)' => $weights[$index],
                    'value' => $values[$index],
                    'labels' => $label,
                ];
            }
        }

        //dd($remainingItems);

        return $remainingItems;
    }

    public function downloadRemainingItems(Request $request)
    {
        $remainingItems = json_decode($request->input('remainingItems'), true);

        // Generate CSV content (similar logic as in getRemainingItems)
        $csvContent = "";

        if (!empty($remainingItems)) {
            $csvHeader = array_keys($remainingItems[0]);
            $csvContent .= implode(",", $csvHeader) . "\n";

            foreach ($remainingItems as $item) {
                $csvContent .= implode(",", $item) . "\n";
            }

            $filename = "remaining_oranges.csv";
            $response = new StreamedResponse(function () use ($csvContent) {
                echo $csvContent;
            });
            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

            $response->send();
        return $response; // Stop further processing (optional)
    }

    // Handle scenario where there are no oranges (optional)
    return redirect()->back()->with('message', 'No remaining oranges to download');
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
