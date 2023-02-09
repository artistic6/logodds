<?php

function highestOccurence($myArray, $second = false){
    $occurences = [];
    foreach($myArray as $myValue){
        if(!isset($occurences[$myValue])) $occurences[$myValue] = 1;
        else $occurences[$myValue] += 1;
    }
    arsort($occurences);
    $targetKeys = array_keys($occurences);
    if( $occurences[$targetKeys[0]] >= 2 ) {
        if(!$second) return [ $targetKeys[0], $occurences[$targetKeys[0]]] ;
        else return [ $targetKeys[1], $occurences[$targetKeys[1]]] ;
    }
    else return [0,0];
}

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
$winProbas = [];
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
    $winProba = [];
    $plaProba = [];
    $winSum = 0;
    $plaSum = 0;
    foreach($tmpOdds as $i => $oddsTmp){
            $oddsW = $oddsTmp['WIN'];
            $oddsP = $oddsTmp['PLA'];
            $winProba[$i] = 100 * (log($oddsW) / $oddsW) / exp(1);
            $winSum += $winProba[$i];
            $plaProba[$i] = 100 / $oddsP;
            $plaSum += $plaProba[$i];
        }
    foreach($tmpOdds as  $i => $oddsTmp){
        $oddsW = $oddsTmp['WIN'];
        $oddsP = $oddsTmp['PLA'];
        $winProba[$i] = round( $winProba[$i] * 100 / $winSum, 2);
        $plaProba[$i] = round( $plaProba[$i] * 100 / $plaSum, 2);
    }
    arsort($winProba);
    $winProbas[$r] = $winProba;
    arsort($plaProba);
    $placeProbas[$r] = $plaProba;
}

$outFile = $currentDir . DIRECTORY_SEPARATOR . $raceDate . ".php";

$outtext = "return [\n";

for ($raceNumber = 1; $raceNumber <= $totalRaces; $raceNumber++) {
    if(!isset($winProbas[$raceNumber])) continue;
    if( count($winProbas[$raceNumber]) < 12 ) continue;

    $selected[$raceNumber] = [];

    $winArray = $winProbas[$raceNumber];
    $plaArray = $placeProbas[$raceNumber];

    $win = determinePlace($winArray, $blacks, $reds);
    $winners[] = $win;
    $selected[$raceNumber][] = $win;

    $Place = determinePlace($plaArray, $blacks, $reds);
    $selected[$raceNumber][] = $Place;

    unset($plaArray[$Place]);
    $Place = determinePlace($plaArray, $blacks, $reds);
    $selected[$raceNumber][] = $Place;

    unset($plaArray[$Place]);
    $Place = determinePlace($plaArray, $blacks, $reds);
    $selected[$raceNumber][] = $Place;  
    
}

$mostCommonWinnerInfo = highestOccurence($winners);
$mostCommonWinner = $mostCommonWinnerInfo[0];
$mostCommonWinnerTimes = $mostCommonWinnerInfo[1];
$secondMostCommonWinnerInfo = highestOccurence($winners, true);
$secondMostCommonWinner = $secondMostCommonWinnerInfo[0];
$secondMostCommonWinnerTimes = $secondMostCommonWinnerInfo[1];

$mostCommonWinners = [ $mostCommonWinner ];
if($secondMostCommonWinnerTimes == $mostCommonWinnerTimes) $mostCommonWinners[] = $secondMostCommonWinner;
$outtext = "<?php\n\n";

$outtext .= "/**\tMost Common Occurence */\t\n";
$outtext .= "/**\tNumber " . $mostCommonWinner . " shows " . $mostCommonWinnerTimes . " Times */\n";
$outtext .= "/**\tSecond Most Common Occurence */\t\n";
$outtext .= "/**\tNumber " . $secondMostCommonWinner . " shows " . $secondMostCommonWinnerTimes . " Times */\n\n";

$outtext .= "return [\n";

for ($raceNumber = 1; $raceNumber <= $totalRaces; $raceNumber++) {
    if(!isset($winProbas[$raceNumber])) continue;
    if( count($winProbas[$raceNumber]) < 12 ) continue;
    $selectedValues = array_unique(array_values($selected[$raceNumber])); 
    foreach($mostCommonWinners as $mostCommonWinnerNumber){
        if(in_array($mostCommonWinnerNumber, $selectedValues)){
            if(count($selectedValues) == 3) {
                $outtext .= "\t'Race $raceNumber' => \n\t[\n\t\t'Win' => [" . implode(", ", $selectedValues) . "]\n";
                $outtext .= "\t],\n";
            }
            elseif(count($selectedValues) == 4 && $selected[$raceNumber][0] == $mostCommonWinnerNumber){
                $outtext .= "\t'Race $raceNumber' => \n\t[\n\t\t'Win, Qin' => [" . implode(", ", $selectedValues) . "]\n";
                $outtext .= "\t],\n";
            }
        }
    }
    $winArray = $winProbas[$raceNumber];
    $plaArray = $placeProbas[$raceNumber];

    
}

$outtext .= "];\n\n";

$outtext .= "?>\n";

file_put_contents($outFile, $outtext);
