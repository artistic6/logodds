<?php

if(!isset($argv[1])) die("Race Date Not Entered!!\n");

$raceDate = trim($argv[1]);
$currentDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate;

$oneFile = $currentDir . DIRECTORY_SEPARATOR . "1.php";
$twoFile = $currentDir . DIRECTORY_SEPARATOR . "2.php";
if(!file_exists($oneFile) || !file_exists($twoFile)){
    exit("No input files!");
}
$oneData = include($oneFile);
$twoData = include($twoFile);

$outFile = $currentDir . DIRECTORY_SEPARATOR . "bets.php";

$outtext = "<?php\n\n";
$outtext .= "return [\n";

foreach($oneData as $raceNumber => $oneRaceDate){
    $wins = $oneData[$raceNumber]["wins"];
    if(!empty($wins)){
        $favoritePlace = true;
        foreach($wins as $winsItem){
            if(count($winsItem) !== 1) $favoritePlace = false;
        }
        if($favoritePlace){
            $racetext = "\t'$raceNumber' => [\n";
            $racetext .= "\t\t/**\n";
            $racetext .= "\t\tRace $raceNumber\n";
            $racetext .= "\t\t*/\n";
            $racetext .= "\t\t'Place' =>  '" . $oneData[$raceNumber]["Favorite"] . "',\n";
            $racetext .= "\t],\n";
            $outtext .= $racetext;
        }
    }
    if(isset($oneRaceDate["Place"])){
        $twoRaceData = $twoData[$raceNumber];
        if(!empty($twoRaceData["Sure Place"])) $placeBet = $twoRaceData["Sure Place"];
        else $placeBet = $oneRaceDate["Place"];
        $qplBet = $oneRaceDate["QQPL"];
        $racetext = "\t'$raceNumber' => [\n";
        $racetext .= "\t\t/**\n";
        $racetext .= "\t\tRace $raceNumber\n";
        $racetext .= "\t\t*/\n";
        $racetext .= "\t\t'Place' =>  '" . $placeBet . "',\n";
        $racetext .= "\t\t'QQPL' =>  '" . $qplBet . "',\n";
        $racetext .= "\t],\n";
        $outtext .= $racetext;
    }
}

$outtext .= "];\n";

file_put_contents($outFile, $outtext);

?>
