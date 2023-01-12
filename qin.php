<?php

function numericalValue($n){
    $tens = intdiv($n, 10);
    $units = $n - 10 * $tens;
    return $tens + $units;
}

if(!isset($argv[1])) die("Race Date Not Entered!!\n");

$raceDate = trim($argv[1]);
$currentDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate;

$allOdds = include($currentDir . DIRECTORY_SEPARATOR . "odds.php");
$SETS = include($currentDir . DIRECTORY_SEPARATOR . "sets.php");
$SELECTED = include($currentDir . DIRECTORY_SEPARATOR . "selected.php");

$probas = [];

$reds = [1, 3, 5, 7, 9, 12, 14, 16, 18, 
         19, 21, 23, 25, 27, 30, 32, 34, 36];

$blacks = [2, 4, 6, 8, 10, 11, 13, 15, 17, 20,
          22, 24, 26, 28, 29, 31, 33, 35];

$totalRaces = count($allOdds);

for($r=1; $r <= $totalRaces; $r++){
    if(!isset($allOdds[$r])) continue;
    $odds = $allOdds[$r];
    $proba = [];
    $sum = 0;
    foreach($odds as $i => $oddsIV){
        $oddsI = $oddsIV['WIN'];
            $proba[$i] = 100 * (log($oddsI) / $oddsI) / exp(1);
            $sum += $proba[$i];
            }
    foreach($odds as  $i => $oddsI){
        //adjust to 100 percentage
        $proba[$i] = round( $proba[$i] * 100 / $sum, 2);
    }
    arsort($proba);
    $probas[$r] = $proba;
}

$outFile = $currentDir . DIRECTORY_SEPARATOR . $raceDate . "-QIN.php";


$outtext = "<?php\n\n";
$outtext .= "return [\n";

for ($raceNumber = 1; $raceNumber <= $totalRaces; $raceNumber++) {
    if(!isset($probas[$raceNumber])) continue;

    $racetext = "";

    $tmpArray = $probas[$raceNumber];
    $runners = array_keys($tmpArray);
    if(count($runners) < 11) continue;
    $racetext .= "\t'$raceNumber' => [\n";
    $sets = $SETS[$raceNumber];
    $selected = array_values(array_unique($SELECTED[$raceNumber]));
    $places = [];

    foreach($sets as $setK){
        if(count($setK) == 2){
            $racetext .= "\t\t'PLA' =>  '" . implode(", ", $setK) . "',\n";
            $places = array_merge($places, $setK);
        }
    }

    $setsValues = array_values(array_unique(array_merge($sets['Set A'], $sets['Set B'], $sets['Set C'])));
    $qin = array_slice($setsValues, 0, 3);
    sort($qin);

    if(count($selected) == 4){
        if(empty($places)){
            $racetext .= "\t\t'Win/Qin' =>  '" . implode(", ", $selected) . "',\n";
        }
        else{
            $racetext .= "\t\t'Win' =>  '" . implode(", ", $selected) . "',\n";
            $tmpValues = array_unique(array_values(array_merge($places, $selected)));
            if(count($tmpValues) === 5){
                sort($tmpValues);
                $racetext .= "\t\t'Qin/Trio/F4' =>  '" . implode(", ", $tmpValues) . "',\n";
            }
        }
    }

    $racetext .= "\t],\n";

    $showRace = !empty($places) || (count($selected) === 4);
    if($showRace) {
        $outtext .= $racetext;
    }
}

$outtext .= "];\n";

file_put_contents($outFile, $outtext);
