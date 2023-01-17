<?php

function numericalValue($n){
    $tens = intdiv($n, 10);
    $units = $n - 10 * $tens;
    return $tens + $units;
}

if(!isset($argv[1])) die("Race Date Not Entered!!\n");

$raceDate = trim($argv[1]);
$currentDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate;
$outFile = $currentDir . DIRECTORY_SEPARATOR . $raceDate . ".php";

if(file_exists($outFile)) {
    $previousBets = include($outFile);
}

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
    $newPla = [];
    $winners = [];
    $newWin = [];
    $qinners = [];
    $newQQP = [];
    $triers = [];
    $newTrio = [];

    if(isset($previousBets[$raceNumber]['PLA'])){
        $previous = $previousBets[$raceNumber]['PLA'];
        $newPla = explode(", ", $previous);
    }

    if(isset($previousBets[$raceNumber]['Win'])){
        $previous = $previousBets[$raceNumber]['Win'];
        $newWin = explode(", ", $previous);
    }

    if(isset($previousBets[$raceNumber]['QQP'])){
        $previous = $previousBets[$raceNumber]['QQP'];
        $newQQP = explode(", ", $previous);
    }

    if(isset($previousBets[$raceNumber]['Trio-F4'])){
        $previous = $previousBets[$raceNumber]['Trio-F4'];
        $newTrio = explode(", ", $previous);
    }

    foreach($sets as $setK){
        if(count($setK) == 2){
            $places = array_merge($places, $setK);
        }
    }
    foreach($places as $place){
        if(!in_array($place, $newPla)) $newPla[] = $place;
    }
    sort($newPla);
    if(!empty($newPla)){
        $racetext .= "\t\t'PLA' =>  '" . implode(", ", $newPla) . "',\n";
    }

    if(count($selected) == 4){
        $winners = $selected;
    }
    foreach($winners as $winner){
        if(!in_array($winner, $newWin)) $newWin[] = $winner;
    }
    sort($newWin);
    if(!empty($newWin)){
        $racetext .= "\t\t'Win' =>  '" . implode(", ", $newWin) . "',\n";
    }

    $qinners = array_slice($sets['Set A'], 0, 4);
    foreach($qinners as $qinner){
        if(!in_array($qinner, $newQQP)) $newQQP[] = $qinner;
    }
    sort($newQQP);
    if(!empty($newQQP)){
        $racetext .= "\t\t'QQP' =>  '" . implode(", ", $newQQP) . "',\n";
    }

    if((count($selected) == 4) && !empty($places)){
        $tmpValues = array_unique(array_values(array_merge($places, $selected)));
        if(count($tmpValues) === 5){
            sort($tmpValues);
            $triers = $tmpValues;
        }
    }
    foreach($triers as $trier){
        if(!in_array($trier, $newTrio)) $newTrio[] = $trier;
    }
    sort($newTrio);
    if(!empty($newTrio)){
        $racetext .= "\t\t'Trio-F4' =>  '" . implode(", ", $newTrio) . "',\n";
    }
    
    $racetext .= "\t],\n";

    $outtext .= $racetext;
}

$outtext .= "];\n";

file_put_contents($outFile, $outtext);
