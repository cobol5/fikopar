<?php
set_time_limit(0);
date_default_timezone_set('Europe/Istanbul');
include 'PHPExcel/IOFactory.php';



$starttime = "";

function start_time() {
    $mtime = microtime(); 
    $mtime = explode(" ",$mtime); 
    $mtime = $mtime[1] + $mtime[0]; 
    $GLOBALS['starttime'] = $mtime; 
}
function end_time($msg) {
    $mtime = microtime(); 
    $mtime = explode(" ",$mtime); 
    $mtime = $mtime[1] + $mtime[0]; 
    $endtime = $mtime; 
    $totaltime = ($endtime - $GLOBALS['starttime']); 
    echo "$msg in #". number_format($totaltime, 6) ." seconds.\n"; 
}

function toCP857($data) {
    return iconv('UTF-8','CP857', $data);
}
function toUTF8($data) {
    return iconv('CP857','UTF-8', $data);
}


/* AKTARILACAK KATALOG NUMARASINI */

$katNo = '0001';


/*
 *
 *      VERİLER EXCELDEN OKUMA
 *
*/
$inputXLSFileName = '\\\\192.168.1.2\\Fiyat Listeleri\\DİZEL TARIM FİYAT LİSTESİ.xls';
$readSheet = 'FİYAT LİSTESİ';

start_time();
//$output = shell_exec('copy "' . iconv('UTF-8','ISO-8859-9',$inputXLSFileName) . '" c:\\windows\\temp\\temp.xls /Y');
//echo $output;

$objReader = PHPExcel_IOFactory::createReader("Excel5");
$objReader->setLoadSheetsOnly( array($readSheet) );
$objPHPExcel = $objReader->load(iconv('UTF-8','ISO-8859-9',$inputXLSFileName));
$objWorksheet = $objPHPExcel->getActiveSheet();
$highestRow = $objWorksheet->getHighestRow();
$_xStoks = Array();
for ($row = 8; $row <= $highestRow; ++$row) {

   $xstno = (string) trim(substr($objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),0,15));
   $xprcno = (string) trim(substr($objWorksheet->getCellByColumnAndRow(2, $row)->getValue(),0,30));
   $xoemno = (string) trim(substr($objWorksheet->getCellByColumnAndRow(3, $row)->getValue(),0,30));
   $xtipi =  (string) trim(substr($objWorksheet->getCellByColumnAndRow(4, $row)->getValue(),0,30));
   $xcinsi = (string) trim(substr($objWorksheet->getCellByColumnAndRow(5, $row)->getValue(),0,60));
   $xnote =  (string) trim(substr($objWorksheet->getCellByColumnAndRow(6, $row)->getValue(),0,10));
   $xmarka = (string) trim(substr($objWorksheet->getCellByColumnAndRow(7, $row)->getValue(),0,30));
   $xfiyat = floatVal(str_replace(',','',trim($objWorksheet->getCellByColumnAndRow(8, $row)->getFormattedValue())));
   $xdeg =  (string) trim(substr($objWorksheet->getCellByColumnAndRow(9, $row)->getFormattedValue(),0,8));
   $xpaket = floatVal(trim($objWorksheet->getCellByColumnAndRow(10, $row)->getValue()));
   if($xstno==NULL or $xstno=='')
            continue;
   if($xprcno=='' and $xoemno=='' and $xtipi=='' and $xcinsi=='')
            continue;
   if(isset($xStoks[$xstno])) {
      echo "$stno numarasına ait çift kayıt var !!!\n";
      exit;
   }      
            
    $_xStoks[$xstno] = Array ( "stno"  => $xstno,
                                 "prcno" => $xprcno,
                                 "oemno" => $xoemno,
                                 "tipi"  => $xtipi,
                                 "cinsi" => $xcinsi,
                                 "marka" => $xmarka,
                                 "fiyat" => $xfiyat,
                                 "note"  => $xnote,
                                 "deg"   => $xdeg,
                                 "paket" => $xpaket);        
}
//unlink("c:\\windows\\temp\\temp.xls");
end_time("Excel files read");
/*
 *
 *      VERİLER EXCELDEN OKUMA BİTTİ
 *
*/

/*
 *
 *      VERİLER DOSYADAN OKUMA
 *
*/

start_time(); 
$output = shell_exec("copy data\\stok.dat c:\\windows\\temp /Y");
echo $output;
$output = shell_exec("copy data\\hardet.dat c:\\windows\\temp /Y");
echo $output;
$output = shell_exec("copy data\\passtok.dat c:\\windows\\temp /Y");
echo $output;
end_time("Copy files to temp");

start_time(); 
$tempFile = substr(time(),2,8);

exec('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" LISOKU A="'. $tempFile . ';' . $katNo . ';" C=WINDOWS.CFG');
end_time("Read files from RMCOBOL TEMP(STOK.DAT)"); 

start_time();
$tempFile = "c:\\windows\\temp\\LISAK___$tempFile.xls";
$inFile = fopen($tempFile, 'r');
$_fStoks = Array();
while (($line = fgets($inFile)) !== false) {
   if(trim($line)=='')
        continue;
   $_hdkod = (string) substr($line,0,2);
   $_katno = (string) substr($line,2,4);
   $_stno = (string)  trim(toUTF8(substr($line,6,15)));
   $_prcno = (string) trim(toUTF8(substr($line,21,30)));
   $_oemno = (string) trim(toUTF8(substr($line,51,30)));
   $_tipi = (string)  trim(toUTF8(substr($line,81,30)));
   $_cinsi = (string)  trim(toUTF8(substr($line,111,60)));
   $_marka =  (string) trim(toUTF8(substr($line,171,30)));
   $_min = (string) substr($line,201,16);
   $_max = (string) substr($line,217,16);
   $_fiyat = floatVal(substr($line,233,10) . '.' . substr($line,243,4));
   $_note = (string) trim(toUTF8(substr($line,247,10)));
   $_deg = (string) substr($line,261,2) . substr($line,259,2);
   $_deg = $_deg == '0000' ? '' : $_deg;
   $_paket = floatVal(substr(265,5));
   $_fStoks[$_stno] = Array ( "hdkod"  => $_hdkod,
                                 "stno"  => $_stno,
                                 "prcno" => $_prcno,
                                 "oemno" => $_oemno,
                                 "tipi"  => $_tipi,
                                 "cinsi" => $_cinsi,
                                 "marka" => $_marka,
                                 "min"   => $_min,
                                 "max"   => $_max,
                                 "fiyat" => $_fiyat,
                                 "note"  => $_note,
                                 "deg"   => $_deg,
                                 "paket" => $_paket);
}
fclose($inFile);
end_time("Read files to memory");

start_time();
$outFile = fopen($tempFile, 'w'); 
$opcode = 0;
foreach($_xStoks as $xStok) {
     $stno = $xStok['stno'];
     if(isset($_fStoks[$stno])) {
       $flag = $_fStoks[$stno]['prcno'] == $xStok['prcno'] &
                      $_fStoks[$stno]['oemno'] == $xStok['oemno'] &
                      $_fStoks[$stno]['tipi'] == $xStok['tipi'] &
                      $_fStoks[$stno]['cinsi'] == $xStok['cinsi'] &
                      $_fStoks[$stno]['marka'] == $xStok['marka'] &
                      $_fStoks[$stno]['fiyat'] == $xStok['fiyat'] &
                      $_fStoks[$stno]['note'] == $xStok['note'] &
                      $_fStoks[$stno]['deg'] == $xStok['deg'] &
                      $_fStoks[$stno]['paket'] == $xStok['paket'];
       if($flag){
           unset($_fStoks[$stno]); 
           continue;
       }
       echo var_dump($_fStoks[$stno]) . " =======>>>>>> " . var_dump($xStok) . " update record.\n\n\n";
       $opcode = 2;
     } else {
       echo var_dump($xStok) . " insert record.\n\n\n";
       $opcode = 1;
       $_fStoks[$stno]['min'] = str_pad('0',16);
       $_fStoks[$stno]['max'] = str_pad('0',16);
       $_fStoks[$stno]['hdkod'] = '00';
     }
     $str = sprintf("%1d%2s%4s%-15s%-30s%-30s%-30s%-60s%-30s%16s%16s%14s%-10s%8s%5s\n", 
                 $opcode, $_fStoks[$stno]['hdkod'], $katNo, toCP857($xStok['stno']), toCP857($xStok['prcno']),
                 toCP857($xStok['oemno']), toCP857($xStok['tipi']), toCP857($xStok['cinsi']), toCP857($xStok['marka']),
                 $_fStoks[$stno]['min'], $_fStoks[$stno]['max'],
                 str_pad(number_format($xStok['fiyat'],4,'',''), 14, '0', STR_PAD_LEFT),
                 toCP857($xStok['note']),
                 $xStok['deg']=='' ? '00000000' : 
                   '20' . substr($xStok['deg'],2,2) . substr($xStok['deg'],0,2) . '01',
                 str_pad(number_format($xStok['paket'],0,'',''), 5, '0', STR_PAD_LEFT));
                 
     if(isset($_fStoks[$stno]))
         unset($_fStoks[$stno]);
     fwrite($outFile, $str);
}
foreach($_fStoks as $fStok) {
    if($fStok['hdkod']=='01')
        echo $fStok['stno'] . ' stok silinmiş pasif stoklara taşınacak !!!';
    if($fStok['hdkod']=='02') {
        echo $fStok['stno'] . ' pasif işlem yapma !!!';
        continue;
    }
    $str = sprintf("0%2s%4s%-15s%-30s%-30s%-30s%-60s%-30s%16s%16s%14s%-10s%8s%5s\n",
                 $fStok['hdkod'], $katNo, toCP857($fStok['stno']), toCP857($fStok['prcno']),
                 toCP857($fStok['oemno']), toCP857($fStok['tipi']),  toCP857($fStok['cinsi']), toCP857($fStok['marka']),
                 $fStok['min'], $fStok['max'],
                 str_pad(number_format($fStok['fiyat'],4,'',''), 14, '0', STR_PAD_LEFT),
                 toCP857($fStok['note']),
                 $fStok['deg']=='' ? '00000000' : 
                    '20' . substr($fStok['deg'],2,2) . substr($fStok['deg'],0,2) . '01',
                 str_pad(number_format($fStok['paket'],0,'',''), 5, '0', STR_PAD_LEFT));  
    echo var_dump($fStok) . " delete record.\n\n\n";
    fwrite($outFile, $str);
}
fclose($outFile);
end_time("Compare extraction");

start_time();
exec('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" LISAKT A="'. $tempFile . '" C=WINDOWS.CFG');
end_time("Differences write to RMCOBOL (ethernet)");

unlink($tempFile);
unlink("c:\\windows\\temp\\stok.dat");
unlink("c:\\windows\\temp\\hardet.dat");
unlink("c:\\windows\\temp\\passtok.dat");
?>
