<?php

$currentYear = "2023";

if(!isset($argv[1])) die("Race Date Not Entered!!\n");
else {
    $raceDate = trim($argv[1]);
    if(strlen($raceDate) !== 4) die("Incorrect Day Format!! Use MMDD\n");
}

$tmp = $raceDate;
$tmp[4] = $raceDate[3];
$tmp[3] = $raceDate[2];
$tmp[2]= "-";
$raceDateFormat = $currentYear . "-" . $tmp;

if(!isset($argv[2])) $venue = "ST";
else $venue = trim($argv[2]);

$outDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate;

if (!file_exists($outDir)) {
    mkdir($outDir, 0777, true);
}

$outFile =$outDir . DIRECTORY_SEPARATOR . "odds.php";

$outtext = "<?php\n\n";
$outtext .= "return [\n";

if(isset($argv[3])) $totalRaces = trim($argv[3]);
else $totalRaces = 10;

for($r = 1; $r <= $totalRaces; $r++){

    $outtext .= "\t$r => [\n";

    $oddsJSON = file_get_contents("https://bet.hkjc.com/racing/getJSON.aspx?type=winplaodds&date=$raceDateFormat&venue=$venue&start=$r&end=$r");

    try {
        $odds = json_decode($oddsJSON, true);
        $odds = $odds["OUT"];
    }
    catch (\Exception $e) {
        exit('{"result":"FALSE","message":"Caught exception: ' . $e->getMessage() . '"}');
    }
   
    $pos = strpos($odds, "#PLA");
    $winOdds = substr($odds, 0, $pos);
    $plaOdds = substr($odds, $pos, strlen($odds));

    $winOdds = explode(";",$winOdds);
    $plaOdds = explode(";",$plaOdds);

    $outOdds = [];

    for($k = 1; $k < count($winOdds); $k++) {
        $lineParts = explode("=", $winOdds[$k]);
        $runner = $lineParts[0];
        $currentOdds = $lineParts[1];
        if($currentOdds != "SCR" && $currentOdds != "Scratched" && !empty($currentOdds) && is_numeric($currentOdds)){
            $outOdds[$runner]['win'] = $currentOdds;
        }
    }

    for($k = 1; $k < count($plaOdds); $k++) {
        $lineParts = explode("=", $plaOdds[$k]);
        $runner = $lineParts[0];
        $currentOdds = $lineParts[1];
        if(isset($outOdds[$runner]['win']) ){
            $outOdds[$runner]['place'] = $currentOdds;
        }
    }

    foreach($outOdds as $runner => $oddie) {
        $outtext .= "\t\t$runner => [\n\t\t\t\t'WIN' => " . $oddie['win'] . ",\n";
        $outtext .= "\t\t\t\t'PLA' => " . $oddie['place'] . "\n";
        $outtext .= "\t\t\t],\n";
    }

    $outtext .= "\t],\n";
}

$outtext .= "];\n";

file_put_contents($outFile, $outtext);


