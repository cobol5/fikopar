<?php
set_time_limit(0);
include 'RMReader.php';
include 'status.php';

$datahardet = RMReader::read('DATA\\HARDET.DAT');
$dataharsic = RMReader::read('DATA\\HARSIC.DAT');
$datasayim = RMReader::read('DATA\\SAYIM.DAT');
$dataadet = RMReader::read('DATA\\STKADT.DAT');
$data = Array();
           
function operator($datKey, $adet, $islem) {
    $gir = 0;
    $cik = 0;
    if(isset($GLOBALS['data'][$datKey])) {
        $gir = floatVal(substr($GLOBALS['data'][$datKey],27,12) . '.' . substr($GLOBALS['data'][$datKey],39,4));
        $cik = floatVal(substr($GLOBALS['data'][$datKey],43,12) . '.' . substr($GLOBALS['data'][$datKey],55,4));
    } 
    if($islem == 1)
        $gir += $adet;
    else
        $cik += $adet;
            
    $GLOBALS['data'][$datKey] = sprintf("%4s%-15s%4s%4s%16s%16s", 
           (string) substr($datKey, 0, 4),
           (string) substr($datKey, 4, 15),
           (string) substr($datKey, 19, 4),
           (string) substr($datKey, 23, 4),
           str_pad(number_format($gir,4,'',''),16,'0',STR_PAD_LEFT),
           str_pad(number_format($cik,4,'',''),16,'0',STR_PAD_LEFT));
}
           
foreach($datasayim as $key => $val) {
    $yil = (string) (substr($val,0,4));
    $katno = (string) (substr($val,4,4));
    $stno = (string) substr($val,8,15);
    $depono = (string) substr($val,33,4);
    $adet = floatVal(substr($val, 236,10) . '.' . substr($val,246,4));
    operator($katno . $stno . $yil . $depono, $adet, +1);
    
}
           
foreach($datahardet as $key => $val) {
    $tip = floatVal(substr($val,0,2));
    if($tip == 3 or $tip == 6 or $tip == 9 or $tip == 12 or $tip == 13 or $tip == 14)
        continue;
    $yil = substr($dataharsic[substr($key,0,17)],17,4);
    $katno = (string) (substr($val,27,4));
    $stno = (string) substr($val,31,15);
    $depono = (string) substr($dataharsic[substr($key,0,17)],435,4);
    $adet = floatVal(substr($val, 220,12) . '.' . substr($val,232,4));
    if($tip == 1 or $tip == 2 or $tip == 7 or $tip == 8)
        operator($katno . $stno . $yil . $depono, $adet, -1);
    if($tip == 4 or $tip == 5 or $tip == 10 or $tip == 11)
        operator($katno . $stno . $yil . $depono, $adet, +1);
}

$outFile = fopen("c:\\windows\\temp\\STKADT.CNT", "w");

foreach($dataadet as $key => $val) {

    if(isset($data[$key])) {
        if($val != $data[$key])
            fwrite($outFile, "2" . $data[$key] . "\n");
        unset($data[$key]);
    } else 
        fwrite($outFile, "0" . $val . "\n");
    
}
foreach($data as $key => $val)
    fwrite($outFile, "1" . $val . "\n");

fclose($outFile);

fwrite(STDOUT, "Kontrol ama‡l durdum c:\\windows\\temp\\stkadt.cnt kontrol et !!! [E/H] ");
$char = fgetc(STDIN);
if($char == 'E' or $char == 'e') {
    $WshShell = new COM("WScript.Shell"); 
    $oExec = $WshShell->Run('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" ADETAKT A="C:\WINDOWS\TEMP\STKADT.CNT"', 7, true); 
}
?>
