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
        list($weights, $values, $labels, $quality, $blemishes, $totalItems) = $this->readCsvFile($file->getRealPath());

        $start = microtime(true);
        $resultGDW = $this->knapsackGD_byWeight($weights, $values, $labels, $quality, $blemishes, $capacity);
        $end = microtime(true);
        $timeGDW = number_format($end - $start, 6);

        $start = microtime(true);
        $resultGDV = $this->knapsackGD_byValue($weights, $values, $labels, $quality, $blemishes, $capacity);
        $end = microtime(true);
        $timeGDV = number_format($end - $start, 6);

        $start = microtime(true);
        $resultGDD = $this->knapsackGD_byDensity($weights, $values, $labels, $quality, $blemishes, $capacity);
        $end = microtime(true);
        $timeGDD = number_format($end - $start, 6);

        $start = microtime(true);
        $resultDP = $this->knapsackDP($weights, $values, $labels, $quality, $blemishes, $capacity);
        $end = microtime(true);
        $timeDP = number_format($end - $start, 6);

        $remainingItems = $this->getRemainingItems($weights, $values, $labels, $quality, $blemishes, $resultDP);

        return view('core.result', compact('resultDP', 'resultGDW', 'resultGDV', 'resultGDD', 'weights', 'values', 'labels', 'remainingItems', 'totalItems', 'capacity', 'timeGDW', 'timeGDV', 'timeGDD', 'timeDP'));
    }

    private function readCsvFile($filepath)
    {
        $weights = []; // weight of the fruit
        $values = []; // value of each fruit
        $labels = []; // label of the fruit
        $sizes = [];
        $blemishes = [];
        $quality = [];
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
                $weight_each = $data[$weightIndex]; // convert to float
                $size_each = $data[$sizeIndex]; // convert to float
                $quality_each = $data[$qualityIndex];
                $label_each = $data[$labelIndex];
                $blemishes_each = $data[$blemishesIndex];

                $value = 0.0;

                if($valuesIndex != null) {
                    $value = $data[$valuesIndex];
                } else {
                    $value = $quality_each;
                }

                $weights[] = $weight_each;
                $values[] = (float)$value;
                $sizes[] = $size_each;
                $labels[] = $label_each;
                $quality[] = $quality_each;
                $blemishes[] = $blemishes_each;

                $totalItems++;
            }
            fclose($handle);
            //dd($values);
        }
        return [$weights, $values, $labels, $quality, $blemishes, $totalItems];
    }

    public function knapsackGD_byWeight($weights, $values, $labels, $quality, $blemishes, $capacity) {
        $items = array_map(null, $weights, $values, $labels, $quality, $blemishes);
        //dd($items);

        usort($items, function($a, $b) {
            return $a[0] <=> $b[0];
        });

        $totalWeight = 0.0;
        $totalValue = 0.0;
        $itemsIncluded = [];
        $totalItems = 0;

        //dd($items);

        foreach ($items as $item) {
            if ($totalWeight + $item[0] <= $capacity) {
                // $itemsIncluded[] = $item;
                $itemsIncluded[] = [
                    'labels' => $item[2],
                    'Weight (g)' => $item[0],
                    'Blemishes (Y/N)' => $item[4],
                    'Quality (1-5)' => $item[3],
                    'value' => $item[1]
                ];
                $totalWeight += $item[0];
                $totalValue += $item[1];
                $totalItems++;
            }
        }

        //dd($itemsIncluded);

        return [
            'items' => $itemsIncluded,
            'total_value' => $totalValue,
            'total_weight' => $totalWeight,
            'total_items' => $totalItems
        ];
    }

    public function knapsackGD_byValue($weights, $values, $labels, $quality, $blemishes, $capacity) {
        $items = array_map(null, $weights, $values, $labels, $quality, $blemishes);

        usort($items, function($a, $b) {
            return $b[1] <=> $a[1];
        });

        $totalWeight = 0.0;
        $totalValue = 0.0;
        $itemsIncluded = [];
        $totalItems = 0;

        foreach ($items as $item) {
            if ($totalWeight + $item[0] <= $capacity) {
                //$itemsIncluded[] = $item;
                $itemsIncluded[] = [
                    'labels' => $item[2],
                    'Weight (g)' => $item[0],
                    'Blemishes (Y/N)' => $item[4],
                    'Quality (1-5)' => $item[3],
                    'value' => $item[1]
                ];
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

    public function knapsackGD_byDensity($weights, $values, $labels, $quality, $blemishes, $capacity) {
        $items = array_map(null, $weights, $values, $labels, $quality, $blemishes);

        usort($items, function($a, $b) {
            return ($b[1] / $b[0]) <=> ($a[1] / $a[0]);
        });

        $totalWeight = 0.0;
        $totalValue = 0.0;
        $itemsIncluded = [];
        $totalItems = 0;

        foreach ($items as $item) {
            if ($totalWeight + $item[0] <= $capacity) {
                //$itemsIncluded[] = $item;
                $itemsIncluded[] = [
                    'labels' => $item[2],
                    'Weight (g)' => $item[0],
                    'Blemishes (Y/N)' => $item[4],
                    'Quality (1-5)' => $item[3],
                    'value' => $item[1]
                ];
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

    public function knapsackDP(array $weights, array $values, array $labels, array $quality, array $blemishes, int $capacity): array
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
        $includedItems = $this->backtrackDP($weights, $included, $values, $labels, $quality, $blemishes, $capacity, $n, $totalWeight);
        //dd($includedItems);

        return [
            'items' => $includedItems,
            'total_value' => $dp[$n][$capacity],
            'total_weight' => $totalWeight,
            'total_items' => count($includedItems)
        ];
    }

    public function backtrackDP(array $weights, array $included, array $values, array $labels, array $quality, array $blemishes, int $capacity, int $currentIndex, int &$totalWeight): array
    {
        $includedItems = [];
        if ($currentIndex === 0 || $capacity === 0) {
            return $includedItems;
        }

        if ($included[$currentIndex][$capacity]) {
            $totalWeight += $weights[$currentIndex - 1];
            $includedItems[] = [
                'index' => $currentIndex - 1,
                'labels' => $labels[$currentIndex - 1], // Add labels
                'Weight (g)' => $weights[$currentIndex - 1],
                'Blemishes (Y/N)' => $blemishes[$currentIndex - 1],
                'Quality (1-5)' => $quality[$currentIndex - 1],
                'value' => $values[$currentIndex - 1], // Add quality
            ];
            return array_merge($includedItems, $this->backtrackDP($weights, $included, $values, $labels, $quality, $blemishes, $capacity - $weights[$currentIndex - 1], $currentIndex - 1, $totalWeight));
        } else {
            return $this->backtrackDP($weights, $included, $values, $labels, $quality, $blemishes, $capacity, $currentIndex - 1, $totalWeight);
        }
    }

    public function getRemainingItems($weights, $values, $labels, $quality, $blemishes, $resultDP) {
        $itemsArray = $resultDP['items'];
        //dd($itemsArray);

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
                    'labels' => $label,
                    'Weight (g)' => $weights[$index],
                    'Blemishes (Y/N)' => $blemishes[$index],
                    'Quality (1-5)' => $quality[$index],
                    'value' => $values[$index],
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
