<?php
set_time_limit(0);
date_default_timezone_set('Europe/London');
include 'PHPExcel/IOFactory.php';

$inputFileName = $argv[1];
$inFile = fopen($inputFileName, 'r');
$line = fgets($inFile);
$line = fgets($inFile);
$objPHPExcel = new PHPExcel();
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);

$ft_mahalle     = iconv('CP857', 'UTF-8',substr($line,0,20));
$ft_ilce        = iconv('CP857', 'UTF-8',substr($line,20,20));
$ft_il          = iconv('CP857', 'UTF-8',substr($line,40,20));
$ft_ulke        = iconv('CP857', 'UTF-8',substr($line,60,20));
$ft_baslama     = substr($line,86,2) . "-" . substr($line,84,2) . "-" . substr($line,80,4);
$ft_bitis       = substr($line,94,2) . "-" . substr($line,92,2) . "-" . substr($line,88,4);
$ft_depo        = iconv('CP857', 'UTF-8',substr($line,96,20));
$ft_plasiyer    = iconv('CP857', 'UTF-8',substr($line,116,20));
$ft_kod         = iconv('CP857', 'UTF-8',substr($line,136,20));

$objPHPExcel->getActiveSheet()->setCellValue('H1', $ft_baslama);
$objPHPExcel->getActiveSheet()->setCellValue('H2', $ft_bitis);
$objPHPExcel->getActiveSheet()
            ->getStyle('H1:H2')
            ->getNumberFormat()
            ->setFormatCode("dd-mm-yyyy"); 
            
$objPHPExcel->getActiveSheet()->getCell('B3')->setValueExplicit($ft_depo,
        PHPExcel_Cell_DataType::TYPE_STRING);        
$objPHPExcel->getActiveSheet()->getCell('B4')->setValueExplicit(trim($ft_plasiyer),
        PHPExcel_Cell_DataType::TYPE_STRING);        
$objPHPExcel->getActiveSheet()->getCell('H3')->setValueExplicit($ft_kod,
        PHPExcel_Cell_DataType::TYPE_STRING);        

$objPHPExcel->getActiveSheet()->mergeCells('A1:D2');

$objPHPExcel->getActiveSheet()->getCell('A1')->setValue('HESAP KARTI SİCİL TOPLAMLARI (MİZAN)');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(True);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$objPHPExcel->getActiveSheet()->getCell('G1')->setValue('Başlama :');
$objPHPExcel->getActiveSheet()->getCell('G2')->setValue('Bitiş :');
$objPHPExcel->getActiveSheet()->getCell('G3')->setValue('Özel Kod :');
$objPHPExcel->getActiveSheet()->getCell('A3')->setValue('Depo :');
$objPHPExcel->getActiveSheet()->getCell('A4')->setValue('Plasiyer :');

$objPHPExcel->getActiveSheet()->getStyle('G1:G3')->getFont()->setBold(True);
$objPHPExcel->getActiveSheet()->getStyle('G1:H3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('G1:G3')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

$objPHPExcel->getActiveSheet()->getStyle('A3:A4')->getFont()->setBold(True);
$objPHPExcel->getActiveSheet()->getStyle('A3:B4')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle("A3:B4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->getStyle('A3:A4')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

$objPHPExcel->getActiveSheet()->getCell('A5')->setValue('Hesap No');
$objPHPExcel->getActiveSheet()->getCell('B5')->setValue('Depo');
$objPHPExcel->getActiveSheet()->getCell('C5')->setValue('Hesap Adı');
$objPHPExcel->getActiveSheet()->getCell('D5')->setValue('Yetkili');
$objPHPExcel->getActiveSheet()->getCell('E5')->setValue('Telefonlar');
$objPHPExcel->getActiveSheet()->getCell('F5')->setValue('Mahalle/İlçe/Şehir/Ülke');
$objPHPExcel->getActiveSheet()->getCell('G5')->setValue('Plasiyer');
$objPHPExcel->getActiveSheet()->getCell('H5')->setValue('Bakiye');

$objPHPExcel->getActiveSheet()->getStyle('A5:H5')->getFont()->setBold(True);
$objPHPExcel->getActiveSheet()->getStyle('A5:H5')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A5:H5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->getStyle('A5:H5')->getBorders()->applyFromArray(
                    array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK)
));

$row = 6;

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);   
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);   
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);   
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(26);   
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);   
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);   
         
   
while (($line = fgets($inFile)) !== false) {
   if(trim($line)=="")
        continue;
   $hesapno     = iconv('CP857', 'UTF-8',substr($line,0,15));  
   $depo        = iconv('CP857', 'UTF-8',substr($line,15,20)); 
   $hesapadi    = iconv('CP857', 'UTF-8',substr($line,35,50)); 
   $yetkili     = iconv('CP857', 'UTF-8',substr($line,85,40)); 
   $tel         = iconv('CP857', 'UTF-8',substr($line,125,60));
   $mahalle     = iconv('CP857', 'UTF-8',substr($line,185,20));
   $ilce        = iconv('CP857', 'UTF-8',substr($line,205,20));
   $il          = iconv('CP857', 'UTF-8',substr($line,225,20));
   $ulke        = iconv('CP857', 'UTF-8',substr($line,245,20));
   $plasiyer    = iconv('CP857', 'UTF-8',substr($line,265,60));
   $bakiye      = iconv('CP857', 'UTF-8',trim(str_replace(',','.',substr($line,325,16))));
   
   $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row)->setValueExplicit(trim($hesapno),
        PHPExcel_Cell_DataType::TYPE_STRING);
   $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $row)->setValue(trim($depo),
        PHPExcel_Cell_DataType::TYPE_STRING);
   $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, $row)->setValueExplicit(trim($hesapadi) . " " . trim($yetkili), 
        PHPExcel_Cell_DataType::TYPE_STRING);
   $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $row)->setValueExplicit(trim($tel),
        PHPExcel_Cell_DataType::TYPE_STRING);
   $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $row)->setValueExplicit(
     trim($mahalle) . "/" . trim($ilce) . trim($il) . "/" . trim($ulke),
        PHPExcel_Cell_DataType::TYPE_STRING);
   $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, $row)->setValueExplicit(trim($plasiyer),
        PHPExcel_Cell_DataType::TYPE_STRING);
   $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(6, $row)->setValueExplicit($bakiye,
        PHPExcel_Cell_DataType::TYPE_NUMERIC);
   $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(6, $row)->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
   $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(30);
   $objPHPExcel->getActiveSheet()->getStyle("A$row:G$row")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
   $objPHPExcel->getActiveSheet()->getStyle("A$row:G$row")->getAlignment()->setWrapText(true);

   $row++;
}
fclose($inFile);
$row--;
$objPHPExcel->getActiveSheet()->getStyle("A6:G$row")->getBorders()->applyFromArray(
                    array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN)
));
$objPHPExcel->getActiveSheet()->getStyle("A$row:H$row")->getBorders()->applyFromArray(
                    array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK)
));
$row++;
 $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->getCell("E$row")->setValue('TOPLAM');
$objPHPExcel->getActiveSheet()->getStyle("E$row")->getFont()->setBold(True);
$objPHPExcel->getActiveSheet()->getStyle("E$row")->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->setCellValue("G$row","=SUM(G6:G" . ($row - 1) .")");
$objPHPExcel->getActiveSheet()->getStyle("G$row")->getNumberFormat()->setFormatCode('#,##0.00');
$objPHPExcel->getActiveSheet()->getStyle("G$row")->getFont()->setBold(True);
$objPHPExcel->getActiveSheet()->getStyle("G$row")->getFont()->setSize(12);


$objPHPExcel->getActiveSheet()->getPageSetup()->setPrintArea("A1:G$row");
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);

$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.50);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.20);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.20);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.50);

// $objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
// $objPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&RSAYFA &P/&N');
$objPHPExcel->getActiveSheet()->setShowGridlines(false);

$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 6);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);

$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
$objWriter->save($inputFileName);
pclose(popen("start /B excel.exe /e " . $inputFileName, "r"));
?>
