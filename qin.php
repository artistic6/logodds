<?php

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
    
    if(!empty($newPla)){
        $racetext .= "\t\t'PLA' =>  '" . implode(", ", $newPla) . "',\n";
    }

    if(count($selected) == 4){
        $winners = $selected;
    }
    foreach($winners as $winner){
        if(!in_array($winner, $newWin)) $newWin[] = $winner;
    }
    
    if(!empty($newWin)){
        $racetext .= "\t\t'Win' =>  '" . implode(", ", $newWin) . "',\n";
    }

    $qinners = array_slice($sets['Set A'], 0, 4);
    foreach($qinners as $qinner){
        if(!in_array($qinner, $newQQP)) $newQQP[] = $qinner;
    }
    
    if(!empty($newQQP)){
        $racetext .= "\t\t'QQP' =>  '" . implode(", ", $newQQP) . "',\n";
    }

    if((count($selected) == 4) && !empty($places)){
        $tmpValues = array_unique(array_values(array_merge($places, $selected)));
        if(count($tmpValues) === 5){
            $triers = $tmpValues;
        }
    }
    foreach($triers as $trier){
        if(!in_array($trier, $newTrio)) $newTrio[] = $trier;
    }
    
    if(!empty($newTrio)){
        $racetext .= "\t\t'Trio-F4' =>  '" . implode(", ", $newTrio) . "',\n";
    }

    $Iwin = array_intersect($newPla, $newWin, $newQQP);
    if(count($Iwin) >= 2){
        $racetext .= "\t\t'I-win' =>  '" . implode(", ", $Iwin) . "',\n";
    }

    $I1 = array_intersect($newPla, $newWin);
    if(count($I1) >= 1){
        $racetext .= "\t\t'I-1' =>  '" . implode(", ", $I1) . "',\n";
    }
        
    $racetext .= "\t],\n";

    $outtext .= $racetext;
}

$outtext .= "];\n";

file_put_contents($outFile, $outtext);
