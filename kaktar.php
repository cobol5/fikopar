<?php
set_time_limit(0);
date_default_timezone_set('Europe/London');
include 'PHPExcel/IOFactory.php';

if (!isset($argv[1])) {
    echo "Aktarmak istediğiniz excel dosyasını parametre olarak veriniz !!";
    exit;
}
$inputFileName = $argv[1];

$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
$objWorksheet = $objPHPExcel->getActiveSheet();
$highestRow = $objWorksheet->getHighestRow(); // e.g. 10
$highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
$outFile = fopen("c:/windows/temp/temp.txt", 'w');
$stoknos = Array();
for ($row = 8; $row <= $highestRow; ++$row) {

   $stno = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(0, $row)->getFormattedValue());
   $prcno = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(1, $row)->getFormattedValue());
   $oemno = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(2, $row)->getFormattedValue());
   $tipi = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(3, $row)->getFormattedValue());
   $cinsi = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(4, $row)->getFormattedValue());
   $note = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(5, $row)->getFormattedValue());
   $marka = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(6, $row)->getFormattedValue());
   $fiyat = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(7, $row)->getFormattedValue());
   $deg = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(8, $row)->getFormattedValue());
   $paket = iconv('UTF-8', 'CP857', $objWorksheet->getCellByColumnAndRow(9, $row)->getFormattedValue());
   
  if($stno==NULL or trim($stno)=='')
            continue;
  if(trim($prcno)=='' and trim($oemno)=='' and trim($tipi)=='' and trim($cinsi)=='')
            continue;
            
  $str = sprintf("%-15s*%-30s*%-30s*%-30s*%-60s*%-10s*%-30s*%16s*%-4s*%5s\n", 
                 substr($stno,0,15), substr($prcno,0,30), substr($oemno,0,30),
                 substr($tipi,0,30), substr($cinsi,0,60), substr($note,0,10), //NOT
                 substr($marka,0,30),substr($fiyat,0,16), substr($deg,0,4),  //DEG
                 substr($paket,0,5));
  fwrite($outFile, $str);
  if(isset($stoknos[$stno])) {
      echo "$stno numarasına ait çift kayıt var !!!\n";
      exit;
  }
  $stoknos[$stno]=1;
}
fclose($outFile);
exec('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" KAKTAR A="C:\WINDOWS\TEMP\TEMP.TXT" C=WINDOWS.CFG');
$inFile = fopen("C:/windows/temp/temp.txt", 'r');
if(!$inFile)
  exit;
$objPHPExcel = new PHPExcel();
$myWorkSheet = Array(new PHPExcel_Worksheet($objPHPExcel, 'Yeni Eklenenler'),
new PHPExcel_Worksheet($objPHPExcel, 'Fiyatı Değişenler'),
new PHPExcel_Worksheet($objPHPExcel, 'Silinenler'));
$objPHPExcel->addSheet($myWorkSheet[2], 0);
$objPHPExcel->addSheet($myWorkSheet[1], 0);
$objPHPExcel->addSheet($myWorkSheet[0], 0);
$i = Array(1,1,1);
while (($line = fgets($inFile)) !== false) {
   $stno = substr($line,0,15);
   $prcno = substr($line,15,30);
   $oemno = substr($line,45,30);
   $tipi = substr($line,75,30);
   $cinsi = substr($line,105,60);
   $marka = substr($line,165,30);
   $fiyat = substr($line,195,10) . '.' . substr($line,205,4);
   $eskifiyat = substr($line,209,10) . '.' . substr($line,219,4);
   $tip = substr($line,223,2);
   if(trim($tip)=='') continue;
   $tip = $tip - 1;
   $myWorkSheet[$tip]->getCellByColumnAndRow(0, $i[$tip])->setValue(iconv('CP857', 'UTF-8', $stno));
   $myWorkSheet[$tip]->getCellByColumnAndRow(1, $i[$tip])->setValue(iconv('CP857', 'UTF-8',$prcno));
   $myWorkSheet[$tip]->getCellByColumnAndRow(2, $i[$tip])->setValue(iconv('CP857', 'UTF-8',$oemno));
   $myWorkSheet[$tip]->getCellByColumnAndRow(3, $i[$tip])->setValue(iconv('CP857', 'UTF-8',$tipi));
   $myWorkSheet[$tip]->getCellByColumnAndRow(4, $i[$tip])->setValue(iconv('CP857', 'UTF-8',$cinsi));
   $myWorkSheet[$tip]->getCellByColumnAndRow(5, $i[$tip])->setValue(iconv('CP857', 'UTF-8',$marka));
   
   if($tip==1) {
    $myWorkSheet[$tip]->getCellByColumnAndRow(6, $i[$tip])->setValue(iconv('CP857', 'UTF-8',$eskifiyat));
    $myWorkSheet[$tip]->getCellByColumnAndRow(7, $i[$tip])->setValue(iconv('CP857', 'UTF-8',$fiyat));
   } else {
    $myWorkSheet[$tip]->getCellByColumnAndRow(6, $i[$tip])->setValue(iconv('CP857', 'UTF-8',$fiyat)); 
   }
   $i[$tip]++;     
}
foreach($myWorkSheet as $workSheet) {
 $workSheet->getColumnDimension('A')->setWidth(12);
 $workSheet->getColumnDimension('B')->setWidth(12);
 $workSheet->getColumnDimension('C')->setWidth(12);
 $workSheet->getColumnDimension('D')->setWidth(12);
 $workSheet->getColumnDimension('E')->setWidth(40);
 $workSheet->getColumnDimension('F')->setWidth(12);
 $workSheet->getColumnDimension('G')->setWidth(10);
 $workSheet->getColumnDimension('H')->setWidth(10);
 $highestRow = $workSheet->getHighestRow();
 $workSheet->getStyle('G1:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
 $workSheet->getStyle('H1:H' . $highestRow)->getNumberFormat()->setFormatCode('#,##0.00');
}
$objPHPExcel->removeSheetByIndex(3);
$objPHPExcel->setActiveSheetIndex(0);

fclose($inFile);
unlink('c:/windows/temp/temp.txt');
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
$logFile = str_replace('.xls', '_log.xls',$inputFileName);
$objWriter->save($logFile);
exec("excel /e " . $logFile);
?>
