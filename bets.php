<?php

if(!isset($argv[1])) die("Race Date Not Entered!!\n");

$blacks = [2, 4, 6, 8, 10, 11, 13, 15, 17, 20,
          22, 24, 26, 28, 29, 31, 33, 35];

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
    if(isset($oneRaceDate["Sure Place"])){
        $racetext = "\t'$raceNumber' => [\n";
        $racetext .= "\t\t/**\n";
        $racetext .= "\t\tRace $raceNumber\n";
        $racetext .= "\t\t*/\n";
        $racetext .= "\t\t'Place(100)' =>  '" . $oneRaceDate["Sure Place"] . "',\n";
        $racetext .= "\t],\n";
        $outtext .= $racetext;
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
        $racetext .= "\t\t'Place(100)' =>  '" . $placeBet . "',\n";
        $racetext .= "\t\t'QQPL (10) ' =>  '" . $qplBet . "',\n";
        $racetext .= "\t],\n";
        $outtext .= $racetext;
    }
}

$outtext .= "];\n";

file_put_contents($outFile, $outtext);

?>
