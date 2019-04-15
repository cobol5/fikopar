<?php
class LOGExcel {
    private $objPHPExcel;
    private $myWorkSheet;
    private $i;
    function __construct() {
        $this->objPHPExcel = new PHPExcel();
        $this->myWorkSheet = Array(new PHPExcel_Worksheet($this->objPHPExcel, 'Yeni Eklenenler'),
                         new PHPExcel_Worksheet($this->objPHPExcel, 'Fiyatı Değişenler'),
                         new PHPExcel_Worksheet($this->objPHPExcel, 'Silinenler'));
        $this->objPHPExcel->addSheet($this->myWorkSheet[2], 0);
        $this->objPHPExcel->addSheet($this->myWorkSheet[1], 0);
        $this->objPHPExcel->addSheet($this->myWorkSheet[0], 0);
        $this->i = Array(1, 1, 1);
    }
    
    public function addLine($tip, $line) {
        $tip = $tip - 1;
        $this->myWorkSheet[$tip]->getCellByColumnAndRow(0, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8', $line['stno']));
        $this->myWorkSheet[$tip]->getCellByColumnAndRow(1, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8',$line['prcno']));
        $this->myWorkSheet[$tip]->getCellByColumnAndRow(2, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8',$line['oemno']));
        $this->myWorkSheet[$tip]->getCellByColumnAndRow(3, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8',$line['tipi']));
        $this->myWorkSheet[$tip]->getCellByColumnAndRow(4, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8',$line['cinsi']));
        $this->myWorkSheet[$tip]->getCellByColumnAndRow(5, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8',$line['marka']));
        if($tip==1) {
            $this->myWorkSheet[$tip]->getCellByColumnAndRow(6, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8',$line['eskifiyat']));
            $this->myWorkSheet[$tip]->getCellByColumnAndRow(7, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8',$line['fiyat']));
        } else {
            $this->myWorkSheet[$tip]->getCellByColumnAndRow(6, $this->i[$tip])->setValue(iconv('CP857', 'UTF-8',$line['fiyat'])); 
        }
        $this->i[$tip]++;     
    }
    public function write($logFile) {
        foreach($this->myWorkSheet as $workSheet) {
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
        $this->objPHPExcel->removeSheetByIndex(3);
        $this->objPHPExcel->setActiveSheetIndex(0);
        $objWriter = new PHPExcel_Writer_Excel5($this->objPHPExcel);
        $objWriter->save($logFile);
        $WshShell = new COM("WScript.Shell"); 
        $oExec = $WshShell->Run("excel /e " . $logFile, 5, false); 
    }
}

?>