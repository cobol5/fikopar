<?php

$katNo = '0001';
$yil = '2015';

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

$vt = mysqli_connect("192.168.1.2","root","root");
if(!$vt) {
    echo "MySqli bağlantı hatası : " . mysqli_connect_errno();
    exit;
}
    
mysqli_select_db($vt,"isletme");

start_time(); 
$tempFile = substr(time(),2,8);
$outFileName = "c:\\windows\\temp\\SAYIM___$tempFile.tmp";
$sorgu = mysqli_query($vt, "select b.stokno,b.parcano,b.oemno,b.tipi,b.parcaadi,b.marka,b.adet,b.maliyeti,b.kdv,b.sayimtarihi,b.rafno
  from hb_sayim_2012 a, hb_sayimdetay_2012 b where a.id = b.idsayim and a.yil = '$yil' and b.stokno<>''");
$outFile = fopen($outFileName, 'w');
while($sonuc = mysqli_fetch_assoc($sorgu)) {
    
    $str = sprintf("%4s%4s%-15s%10s%4s%-30s%-30s%-30s%-60s%-30s%19s%14s%9s%-15s\n",
                    $yil,
                    $katNo,
                    toCP857(substr($sonuc['stokno'],0,15)), 
                    str_pad('',10,'0'),
                    '0001',
                    toCP857(substr($sonuc['parcano'],0,30)),
                    toCP857(substr($sonuc['oemno'],0,30)),
                    toCP857(substr($sonuc['tipi'],0,30)),
                    toCP857(substr($sonuc['parcaadi'],0,60)),
                    toCP857(substr($sonuc['marka'],0,60)),
                    str_pad(number_format($sonuc['maliyeti'], 4,'',''), 19, '0', STR_PAD_LEFT),
                    str_pad(number_format($sonuc['adet'],4,'',''), 14, '0', STR_PAD_LEFT),
                    str_pad(number_format($sonuc['kdv'],4,'',''), 9, '0', STR_PAD_LEFT),
                    toCP857(substr($sonuc['rafno'],0,15)));
    fwrite($outFile, $str);
}
fclose($outFile);
end_time("Reading sayim ($yil) from sql done");
//echo "Row Number is " . mysqli_num_rows($sorgu);
mysqli_close($vt);
start_time();
exec('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" SAYIMAKT A="'. $outFileName . ';' . $yil . ';" C=WINDOWS.CFG');
end_time("Writing records to cobol done"); 
//unlink($outFileName);
?>