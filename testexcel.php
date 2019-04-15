<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
date_default_timezone_set('Europe/Istanbul');
include 'PHPExcel/IOFactory.php';
include 'status.php';

/**  Define a Read Filter class implementing PHPExcel_Reader_IReadFilter  */
class chunkReadFilter implements PHPExcel_Reader_IReadFilter
{
    private $_startRow = 0;

    private $_endRow = 0;

    /**  Set the list of rows that we want to read  */
    public function setRows($startRow, $chunkSize) {
        $this->_startRow    = $startRow;
        $this->_endRow        = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '') {
        //  Only read the heading row, and the rows that are configured in $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
            return true;
        }
        return false;
    }
}

$workbook = iconv("UTF-8","ISO-8859-9","\\\\192.168.1.2\\Fiyat Listeleri\\LİSTE.xls");
$sheet = iconv("UTF-8","ISO-8859-9", "M & U"); // 0001

echo "liste okunuyor ... 1 \n";
$objReader = PHPExcel_IOFactory::createReader("Excel5");
echo "liste okunuyor ... 2 \n";
$objReader->setLoadSheetsOnly( array($sheet) );
echo "liste okunuyor ... 3 \n";
/**  Define how many rows we want to read for each "chunk"  **/
$chunkSize = 20;
/**  Create a new Instance of our Read Filter  **/
$chunkFilter = new chunkReadFilter();
/**  Tell the Reader that we want to use the Read Filter that we've Instantiated  **/
$objReader->setReadFilter($chunkFilter);
$chunkFilter->setRows(6,$chunkSize);
echo "liste okunuyor ... 4 \n";
$objPHPExcel = $objReader->load($workbook);
echo "liste okunuyor ... 5 \n";
$objWorksheet = $objPHPExcel->getActiveSheet();
echo "liste okunuyor ... 6 \n";
$highestRow = $objWorksheet->getHighestRow();
echo "liste okunuyor ... 7 \n";
echo "highestRow : $highestRow";

?>