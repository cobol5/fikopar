<?php
set_time_limit(0);
include 'RMReader.php';
include 'status.php';
$epsilon = 0.00001;

$datakasa = RMReader::read('DATA\\KASA.DAT');
$data = Array();
$datatoplam = Array();
           
foreach($datakasa as $key => $val) {
    $tarih = (string) (substr($val,0,8));
    $sira = floatVal(substr($val,8,5));
    $gelir = floatVal(substr($val,88,13) . '.' . substr($val,101,2));
    $gider = floatVal(substr($val,103,13) . '.' . substr($val,116,2));
    
    if($sira == 0)
        $datatoplam[$tarih] = Array('gelir' => $gelir, 'gider' => $gider);
    else {
        if(isset($data[$tarih])) {
            $data[$tarih]['gelir'] += $gelir;
            $data[$tarih]['gider'] += $gider;
        } else 
            $data[$tarih] = Array('gelir' => $gelir, 'gider' => $gider);
    }
}

$outFile = fopen("c:\\windows\\temp\\kasa.cnt", "w");

foreach($data as $key => $val) {
    if(isset($datatoplam[$key])) {
        if(abs($datatoplam[$key]['gelir']-$data[$key]['gelir']) < $epsilon and
           abs($datatoplam[$key]['gider']-$data[$key]['gider']) < $epsilon)
            continue;
        else {
            fwrite($outFile, sprintf("1%8s%15s%15s\n", $key,
                                     str_pad(number_format($data[$key]['gelir'],2,'',''),15,'0',STR_PAD_LEFT),
                                     str_pad(number_format($data[$key]['gider'],2,'',''),15,'0',STR_PAD_LEFT)));
        }
    } else
        fwrite($outFile, sprintf("0%8s%15s%15s\n", $key,
                                     str_pad(number_format($data[$key]['gelir'],2,'',''),15,'0',STR_PAD_LEFT),
                                     str_pad(number_format($data[$key]['gider'],2,'',''),15,'0',STR_PAD_LEFT)));
}
    
fclose($outFile);

fwrite(STDOUT, "Kontrol ama‡l durdum c:\\windows\\temp\\kasa.cnt kontrol et !!! [E/H] ");
$char = fgetc(STDIN);
if($char == 'E' or $char == 'e') {
    $WshShell = new COM("WScript.Shell"); 
    $oExec = $WshShell->Run('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" KASAAKT A="C:\WINDOWS\TEMP\KASA.CNT"', 7, true); 
}
?>
