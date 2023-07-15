<?php

function numericalValue($n){
    $tens = intdiv($n, 10);
    $units = $n - 10 * $tens;
    return $tens + $units;
}
/**
 * Returns true if array $needle is contained in array of arrays $haystack 
 */
function in_my_array($needle, $haystack){
    foreach($haystack as $comparedTo){
        $shit1 = array_values($needle);
        $shit2 = array_values($comparedTo);
        sort($shit1); 
        sort($shit2);
        $count1 = count($shit1);
        $count2 = count($shit2);
        if($count1 == $count2){
            $coincidences = 0;
            for($compt = 0; $compt < $count1; $compt++) {
                if($shit1[$compt] == $shit2[$compt]) $coincidences++;
            }
            if($coincidences == $count1) return true;
        }
    }
    return false;
}

if(!isset($argv[1])) die("Output File Not Entered!!\n");
if(!isset($argv[2])) die("Race Date Not Entered!!\n");

$step = trim($argv[1]);
$raceDate = trim($argv[2]);
$currentDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate;

$allOdds = include($currentDir . DIRECTORY_SEPARATOR . "getodds.php");

$outFile = $currentDir . DIRECTORY_SEPARATOR . "$step.php";
if(file_exists($outFile)){
    $oldData = include($outFile);
}
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
    foreach($odds as $i => $oddsI){
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

    if(isset($oldData)){
        if(isset($oldData[$raceNumber])){
            $oldRaceData = $oldData[$raceNumber];
            if(isset($oldRaceData['wins'])) $oldWINS = $oldRaceData['wins'];
            if(isset($oldRaceData['SS'])) $oldSS = $oldRaceData['SS'];
            if(isset($oldRaceData['inters'])) $oldINTERS = $oldRaceData['inters'];
            if(isset($oldRaceData['qpl/trio'])) $oldQPLTrio = $oldRaceData['qpl/trio'];
        }
    }
    $racetext = "";

    $tmpArray = $probas[$raceNumber];
    $runners = array_keys($tmpArray);

    if(count($runners) < 10) continue;
    
    $racetext .= "\t'$raceNumber' => [\n";
    $racetext .= "\t\t/**\n";
    $racetext .= "\t\tRace $raceNumber\n";
    $racetext .= "\t\t*/\n";

    $values = array_values($tmpArray);

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

    $trio = array_merge(array_slice($favorites, 0, 3), array_slice($others, 0, 2));

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
    $difference = array_values(array_unique(array_diff($trio, $toWin)));
    $intersection = array_Values(array_unique(array_intersect($toWin, $trio)));

    // $racetext .= "\t\t'F: " . implode(", ", $favorites) . "',\n";
    // $racetext .= "\t\t'O: " . implode(", ", $others) . "',\n";
    
    if(isset($oldWINS)) $wins = $oldWINS;
    else $wins = [];

    if(isset($oldINTERS)) $inters = $oldINTERS;
    else $inters = [];

    if(isset($oldQPLTrio)) $qplTrios = $oldQPLTrio;
    else $qplTrios = [];
    
    if(!empty($difference)) {
        if(!in_my_array($difference, $wins)) $wins[] = $difference;
        $qin = implode(", ", $intersection) . ' X ' . implode(", ", $difference);
        $qinValues = array_values(array_unique(array_merge($intersection, $difference)));
    }
    else{
        $qin = implode(", ", $intersection);
        $qinValues = $intersection;
    } 

    if(!in_my_array($intersection, $inters)) $inters[] = $intersection;

    $iInter = [];
    for($i = 0; $i < count($inters); $i ++){
        for($j = $i + 1; $j < count($inters); $j ++){
            $iInter = array_values(array_unique(array_merge($iInter, array_intersect($inters[$i], $inters[$j]))));
        }
    }

    sort($iInter);

    $selected = array_values(array_unique(array_merge($intersection, $difference)));
    $bSelected = array_intersect($selected, $blacks);
    $rSelected = array_intersect($selected, $reds);
    sort($bSelected); sort($rSelected);
    $qplTrio = array_values(array_unique(array_merge($rSelected, $bSelected)));
    if(!in_my_array($qplTrio, $qplTrios)) $qplTrios[] = $qplTrio;

    $_WIN = [];
    foreach($wins as $winsItem){
        $_WIN = array_values(array_unique(array_merge($_WIN, $winsItem)));
    }

    $SA = array_intersect($_WIN, $qinValues);
    $SB = array_intersect($iInter, $qinValues);
    
    $WINSText = "[";
    $someCounter = 0;
    $someLength = count($wins);
    foreach($wins as $winsItem){
        if(in_array($first1, $winsItem)) $NOPLACE = true;
        $WINSText .= "[" . implode(", ", $winsItem) . "]";
        $someCounter ++;
        if($someCounter < $someLength) $WINSText .= ", ";
    }
    $WINSText .= "]";

    $INTERSText = "[";
    $someCounter = 0;
    $someLength = count($inters);
    foreach($inters as $intersItem){
        $INTERSText .= "[" . implode(", ", $intersItem) . "]";
        $someCounter ++;
        if($someCounter < $someLength) $INTERSText .= ", ";
    }
    $INTERSText .= "]";

    $QPLText = "[";
    $someCounter = 0;
    $someLength = count($qplTrios);
    foreach($qplTrios as $qplItem){
        $QPLText .= "[" . implode(", ", $qplItem) . "]";
        $someCounter ++;
        if($someCounter < $someLength) $QPLText .= ", ";
    }
    $QPLText .= "]";

    $X1 = array_intersect($_WIN, $iInter, $SA);
    $X2 = array_intersect($_WIN, $iInter, $SB);
    $X = array_values(array_unique(array_merge($X1, $X2)));

    $iInter = array_diff($iInter, $X);
    $iInter = array_diff($iInter, [$first1]);

    $racetext .= "\t\t'wins' =>  $WINSText ,\n";
    $racetext .= "\t\t'qpl/trio' =>  $QPLText ,\n";
    $racetext .= "\t\t'inters' =>  $INTERSText ,\n";
    $racetext .= "\t\t'Favorite' =>  '" . $first1. "',\n";
    $racetext .= "\t\t'I' =>  '" . implode(", ", $iInter). "',\n";
    if(isset($NOPLACE)) $racetext .= "\t\t'PLACE' =>  'NO',\n";
    else $racetext .= "\t\t'PLACE' =>  '" . $first1. "',\n";
    $racetext .= "\t],\n";
    unset($qin);
    unset($qinValues);
    unset($oldWINS);
    unset($oldQPLTrio);
    unset($NOPLACE);
    $outtext .= $racetext;
}

$outtext .= "];\n";

file_put_contents($outFile, $outtext);
