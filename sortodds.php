<?php

function numericalValue($n){
    $tens = intdiv($n, 10);
    $units = $n - 10 * $tens;
    return $tens + $units;
}

function determinePlace($tmpArray, $blacks, $reds){
    $runners = array_keys($tmpArray);
    $sBlacks = array_values(array_intersect($runners, $blacks));
    $sReds = array_values(array_intersect($runners, $reds));
    $first1 = $runners[0];
    if(in_array($first1, $blacks)){
       $favorites = $sBlacks;
       $others = $sReds;
    }
    else{
       $favorites = $sReds;
       $others =$sBlacks;
    }
    $qplLeftSide = [$favorites[0], $favorites[1], $favorites[2], $others[0], $others[1]];
    $qplRightSide = [ $others[3], $favorites[count($favorites) - 3], $others[count($others) - 3], end($favorites), end($others) ];
    
    $toWin = [];
    for($indexL = 0; $indexL < count($qplLeftSide); $indexL++) {
        for($indexR = 0; $indexR < count($qplRightSide); $indexR++) {
            $left = $qplLeftSide[$indexL];
            $right = $qplRightSide[$indexR];
            if( 
                (abs(numericalValue($left) - numericalValue($right)) <= 2)
                &&
                (
                    (in_array($left, $sReds) && in_array($right, $sBlacks))
                    || (in_array($left, $sBlacks) && in_array($right, $sReds))
                )
            ){
                if(!in_array($left, $toWin)) $toWin[] = $left;
                if(!in_array($right, $toWin)) $toWin[] = $right;
            }
        }
    }
    $S1 = array_intersect($toWin, $runners);
    return reset($S1);   
}

if(!isset($argv[1])) die("Race Date Not Entered!!\n");

$raceDate = trim($argv[1]);
$currentDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate;

$allOdds = include($currentDir . DIRECTORY_SEPARATOR . "odds.php");
$outFile = $currentDir . DIRECTORY_SEPARATOR . $raceDate . ".php";
if(file_exists($outFile)){
    $oldOdds = include($outFile);
}

$placeProbas = [];

$winners = [];
$selected = [];

$reds = [1, 3, 5, 7, 9, 12, 14, 16, 18, 
         19, 21, 23, 25, 27, 30, 32, 34, 36];

$blacks = [2, 4, 6, 8, 10, 11, 13, 15, 17, 20,
          22, 24, 26, 28, 29, 31, 33, 35];

$totalRaces = count($allOdds);

for($r=1; $r <= $totalRaces; $r++){
    if(!isset($allOdds[$r])) continue;
    $tmpOdds = $allOdds[$r];
    $plaProba = [];
    $plaSum = 0;
    foreach($tmpOdds as $i => $oddsTmp){
            $oddsP = $oddsTmp['PLA'];
            $plaProba[$i] = 100 / $oddsP;
            $plaSum += $plaProba[$i];
        }
    foreach($tmpOdds as  $i => $oddsTmp){
        $oddsP = $oddsTmp['PLA'];
        $plaProba[$i] = round( $plaProba[$i] * 100 / $plaSum, 2);
    }
    arsort($plaProba);
    $placeProbas[$r] = $plaProba;
}

$outtext = "return [\n";

for ($raceNumber = 1; $raceNumber <= $totalRaces; $raceNumber++) {
    if(!isset($placeProbas[$raceNumber])) continue;
    if( count($placeProbas[$raceNumber]) < 12 ) continue;

    $plaArray = $placeProbas[$raceNumber];

    $Place = determinePlace($plaArray, $blacks, $reds);
    $selected[$raceNumber] = $Place;
   
}

$outtext = "<?php\n\n";
$outtext .= "return [\n";

for ($raceNumber = 1; $raceNumber <= $totalRaces; $raceNumber++) {
    if(!isset($placeProbas[$raceNumber])) continue;
    if( count($placeProbas[$raceNumber]) < 12 ) continue;
    if(isset($oldOdds)){
        $selectedArray = $oldOdds["Race $raceNumber"]['Win'];
    }
    else{
        $selectedArray = [];
    }
    $selectedArray[] = $selected[$raceNumber];
    $selectedValues = array_unique(array_values($selectedArray));
    $outtext .= "\t'Race $raceNumber' => \n\t[\n\t\t'Win' => [" . implode(", ", $selectedValues) . "]\n";
    $outtext .= "\t],\n";
}

$outtext .= "];\n\n";

$outtext .= "?>\n";

file_put_contents($outFile, $outtext);
