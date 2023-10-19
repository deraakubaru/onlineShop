<?php

//grid layout
$grid = [
    "########",
    "#......#",
    "#.###..#",
    "#...#.##",
    "#X#....#",
    "########"
];

// Function to find probable item locations
function findProbableLocations($grid, $startX, $startY, $stepsA, $stepsB, $stepsC) {
    $probableLocations = [];

    for ($i = 0; $i < count($grid); $i++) {
        for ($j = 0; $j < strlen($grid[$i]); $j++) {
            if ($grid[$i][$j] === '.') {
                // Check if the item can be found by following the specified steps
                if ($i - $stepsA >= 0 && $i + $stepsC < count($grid) && $j + $stepsB < strlen($grid[$i])) {
                    $probableLocations[] = [$i, $j];
                }
            }
        }
    }

    return $probableLocations;
}

// Find probable item locations
$startX = 4; $startY = 1;  // Starting position
$stepsA = 1; $stepsB = 3; $stepsC = 1;

$probableLocations = findProbableLocations($grid, $startX, $startY, $stepsA, $stepsB, $stepsC);

// Mark probable item locations with '$' on the grid
foreach ($probableLocations as $location) {
    $i = $location[0];
    $j = $location[1];
    $row = str_split($grid[$i]);
    $row[$j] = '$';
    $grid[$i] = implode('', $row);
}

// Display the grid
foreach ($grid as $row) {
    echo $row . "\n";
}
?>
