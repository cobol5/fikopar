<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
date_default_timezone_set('Europe/Istanbul');
include 'PHPExcel/IOFactory.php';
include 'RMReader.php';
include 'status.php';
require 'LOGExcel.php';

$katNo = $argv[1];
$inputXLSFileName = $argv[2];
$readSheet = iconv('ISO-8859-9', 'UTF-8', $argv[3]);
$logEnable = $argv[4];
$askAktar = $argv[5];
$rowStart = $argv[6];
$bolumAktar = $argv[7];

echo "Reading files ... \n";
$pasdata = RMReader::read('DATA\\PASSTOK.DAT', $katNo);
show_status(1, 3);

$data = RMReader::read('DATA\\STOK.DAT', $katNo);
show_status(2, 3);


$objReader = PHPExcel_IOFactory::createReader("Excel5");
$objReader->setLoadSheetsOnly( array($readSheet) );
$objPHPExcel = $objReader->load($inputXLSFileName);
show_status(3, 3);

echo "Comparing contents ...\n";

$objWorksheet = $objPHPExcel->getActiveSheet();
$highestRow = $objWorksheet->getHighestRow();
$xdata = Array();
$xbolum = Array();
$logExcel = new LOGExcel();
$outFile = fopen("c:\\windows\\temp\\temp.txt", 'w');
for ($row = $rowStart; $row <= $highestRow; ++$row) {
    show_status($row, $highestRow);

    $xstno = (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),0,15));
    $xprcno = (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(2, $row)->getValue(),0,30));
    $xoemno = (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(3, $row)->getValue(),0,30));
    $xtipi =  (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(4, $row)->getValue(),0,30));
    $xcinsi = (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(5, $row)->getValue(),0,60));
    $xnote =  (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(6, $row)->getValue(),0,10));
    $xmarka = (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(7, $row)->getValue(),0,30));
    $xfiyat = floatVal(str_replace(',','',trim($objWorksheet->getCellByColumnAndRow(8, $row)->getFormattedValue())));
    $xdeg =  (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(9, $row)->getFormattedValue(),0,4));
    $xpaket = floatVal(rtrim($objWorksheet->getCellByColumnAndRow(10, $row)->getValue()));
    $xmin = floatVal(str_replace(',','',rtrim($objWorksheet->getCellByColumnAndRow(11, $row)->getFormattedValue())));
    $xmax = floatVal(str_replace(',','',rtrim($objWorksheet->getCellByColumnAndRow(12, $row)->getFormattedValue())));
    $bkatno = floatVal(rtrim($objWorksheet->getCellByColumnAndRow(13, $row)->getValue()));
    $bstno = (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(14, $row)->getValue(),0,15));
    $biskonto = floatVal(str_replace(',','',rtrim($objWorksheet->getCellByColumnAndRow(15, $row)->getFormattedValue())));
    $bkdv = floatVal(str_replace(',','',rtrim($objWorksheet->getCellByColumnAndRow(16, $row)->getFormattedValue())));
    
    if($bolumAktar == 1) {
        if($xstno=='' and $xcinsi != '')
            $xbolum[$xcinsi] = Array("sira" => $row - $rowStart);
    }
    
    if($xstno==NULL or $xstno=='')
        continue;
    if($xprcno=='' and $xoemno=='' and $xtipi=='' and $xcinsi=='')
        continue;
    $key = sprintf("%4s%-15s", $katNo, $xstno);
    if(isset($xdata[$key])) {
        fwrite(STDERR, "\n$xstno duplicate key ! \n" . PHP_EOL);
        exit;
    }
    
    $xdeg = $xdeg == '' ? str_pad('0',8, STR_PAD_LEFT) : '20' . substr($xdeg,2,2) . substr($xdeg,0,2) . '01';
    $val = sprintf("%4s%-15s%-30s%-30s%-30s%-60s%-30s%16s%16s%14s%-10s%8s%5s%15s", $katNo,
                 toCP857($xstno), toCP857($xprcno), toCP857($xoemno), toCP857($xtipi),  toCP857($xcinsi), toCP857($xmarka),
                 str_pad(number_format($xmin,4,'',''), 16, '0', STR_PAD_LEFT),
                 str_pad(number_format($xmax,4,'',''), 16, '0', STR_PAD_LEFT),
                 str_pad(number_format($xfiyat,4,'',''), 14, '0', STR_PAD_LEFT),
                 toCP857($xnote), $xdeg, str_pad(number_format($xpaket,0,'',''), 5, '0', STR_PAD_LEFT),
                 str_pad(number_format($row - $rowStart,0,'',''), 15, '0', STR_PAD_LEFT)
                 );  
    $xdata[$key] = $val;
    $bag = sprintf("%4s%-15s%8s%8s", 
                    str_pad(number_format($bkatno,0,'',''), 4, '0', STR_PAD_LEFT),
                    toCP857($bstno), 
                    str_pad(number_format($biskonto,4,'',''), 8, '0', STR_PAD_LEFT),
                    str_pad(number_format($bkdv,4,'',''), 8, '0', STR_PAD_LEFT));
    if(isset($passtok[$key])) {
        fwrite(STDERR, "\n$xstno daha önce hareket görmüş ve silinmiş ! " . PHP_EOL);
        exit;
    }
    if(isset($data[$key])) {
        if($data[$key] == $xdata[$key] && trim($bstno) == '') {
            unset($data[$key]);
            continue;
        } else {
            fwrite($outFile, "2" . $xdata[$key] . $bag . "\n");
            $fiyat = floatVal(substr($data[$key],231,10) . '.' . substr($data[$key],241,4));
            if($xfiyat != $fiyat) {
                $line = Array('stno'=>$xstno, 'prcno'=>$xprcno, 'oemno'=>$xoemno, 
                    'tipi'=>$xtipi, 'cinsi'=>$xcinsi, 'marka'=>$xmarka, 'fiyat' => $xfiyat, 'eskifiyat' => $fiyat, 'sira' => $row - $rowStart);
                $logExcel->addLine(2, $line);
            }
            unset($data[$key]);
        }
    } else {
        $line = Array('stno'=>$xstno, 'prcno'=>$xprcno, 'oemno'=>$xoemno, 
           'tipi'=>$xtipi, 'cinsi'=>$xcinsi, 'marka'=>$xmarka, 'fiyat'=>$xfiyat, 'sira' => $row - $rowStart);
        $logExcel->addLine(1, $line);
        fwrite($outFile, "1" . $xdata[$key] . $bag . "\n" );
    }
    
}
echo "Checking delete rows ...\n";
unset($xdata);
$i = 1;
show_status($i, count($data)+1);
$bag = sprintf("%4s%-15s%8s%8s", '0000', '', '00000000', '00000000');
foreach($data as $dat) {
    $line = Array('stno'=>toUTF8(substr($dat,4,15)), 'prcno'=>toUTF8(substr($dat,19,30)), 'oemno'=>toUTF8(substr($dat,49,30)), 
       'tipi'=>toUTF8(substr($dat,79,30)), 'cinsi'=>toUTF8(substr($dat,109,60)), 'marka'=>toUTF8(substr($dat,169,30)), 
         'fiyat'=>floatVal(substr($dat,231,10) . '.' . substr($dat,241,4)));
         
    $logExcel->addLine(3, $line);
        
    fwrite($outFile, "0" . $dat . $bag . "\n" );
    show_status(++$i, count($data)+1);
}
fclose($outFile);
if($logEnable == 1)
    $logExcel->write("c:\\windows\\temp\\temp.xls");

if($askAktar == 0) {
    bolumAktar($xbolum, $katNo);
    $WshShell = new COM("WScript.Shell"); 
    $oExec = $WshShell->Run('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" LISAKT A="C:\WINDOWS\TEMP\TEMP.TXT" L="z:\fikopar\mysql.dll"', 7, true); 
} else {
    fwrite(STDOUT, toCP857("Değişiklikler yazılsın mı ? [E/H] "));
    $char = fgetc(STDIN);
    if($char == 'E' or $char == 'e') {
        bolumAktar($xbolum, $katNo);
        $WshShell = new COM("WScript.Shell"); 
        $oExec = $WshShell->Run('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" LISAKT A="C:\WINDOWS\TEMP\TEMP.TXT" L="z:\fikopar\mysql.dll"', 7, true); 
    }
}
    
function toCP857($data) {
    return iconv('UTF-8','CP857', $data);
}

function toUTF8($data) {
    return iconv('CP857','UTF-8', $data);
}

function bolumaktar($xbolum, $katNo) {
    echo "Bolumler ve siralar aktariliyor ...\n";

    $db = new mysqli('192.168.1.2:3306', 'root', 'root', 'fikopar');

    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $sql = "CREATE TABLE IF NOT EXISTS BOLUM (
                katno int(4),
                sira int(11),
                bolum varchar(100),
                PRIMARY KEY (katno,bolum)
            );";

    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }
    $db->query("START TRANSACTION");
    
    $insert_query = "insert into bolum(katno,sira,bolum) values (?,?,?)";
    if(!$insert_stmt = $db->prepare($insert_query))
        die("Error create insert statement [" . $db->error . "]");
    $insert_stmt->bind_param("iis", $katNo, $sira, $bolum);
 
    $update_query = "update bolum set sira = ? where katno = ? and bolum = ?";
    if(!$update_stmt = $db->prepare($update_query))
        die("Error create update statement [" . $db->error . "]");
    $update_stmt->bind_param("iis", $katNo, $sira, $bolum);
 
    $delete_query = "delete from bolum where katno = ? and bolum = ?";
    if(!$delete_stmt = $db->prepare($delete_query))
        die("Error create delete statement [" . $db->error . "]");
    $delete_stmt->bind_param("is", $katNo, $bolum);
 
    $sql = 'select * from bolum';

    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }
    $data_size = count($xbolum);
    while($row = $result->fetch_assoc()){
        show_status($data_size - count($xbolum) + 1, $data_size);
        if(isset($xbolum[$row['bolum']])) {
            if($xbolum[$row['bolum']]['sira'] != $row['sira']) {
                $sira = $xbolum[$row['bolum']]['sira'];
                $update_stmt->execute();
                unset($xbolum[$row['bolum']]);
            }
        } else {
            $bolum = $row['bolum'];
            $delete_stmt->execute();
        }
    }
    foreach($xbolum as $key => $val) {
        show_status($data_size - count($xbolum) + 1, $data_size);
        $bolum = $key;
        $sira = $val['sira'];
        $insert_stmt->execute();
        unset($xbolum[$key]);
    }
    $update_stmt->close();
    $insert_stmt->close();
    $delete_stmt->close();
    $db->query("COMMIT");
    $db->close();
}
?>