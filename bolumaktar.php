<?php
set_time_limit(0);
date_default_timezone_set('Europe/Istanbul');
include 'PHPExcel/IOFactory.php';
include 'status.php';

$katno = $argv[1];
$inputXLSFileName = $argv[2];
$readSheet = iconv('ISO-8859-9', 'UTF-8', $argv[3]);
$rowStart = $argv[4];

echo "Reading files ... \n";

$objReader = PHPExcel_IOFactory::createReader("Excel5");
$objReader->setLoadSheetsOnly( array($readSheet) );
$objPHPExcel = $objReader->load($inputXLSFileName);

echo "Comparing contents ...\n";

$db = new mysqli('192.168.1.2:3306', 'root', '1413', 'fikopar');

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

$sql = "CREATE TABLE IF NOT EXISTS BOLUM (
        katno int(4),
        stno varchar(15),
        bolum varchar(60),
        sira int(11),
        PRIMARY KEY (katno,stno)
    );";

if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
}
$db->query("START TRANSACTION");
$db->query("truncate table bolum");

$insert_query = "insert into bolum values (?,?,?,?)";
if(!$insert_stmt = $db->prepare($insert_query))
    die("Error create insert statement [" . $db->error . "]");

$insert_stmt->bind_param("issi", $katno, $stno, $bolum, $row);
 
    
$objWorksheet = $objPHPExcel->getActiveSheet();
$highestRow = $objWorksheet->getHighestRow();
$bolum = '';

for ($row = $rowStart; $row <= $highestRow; ++$row) {
    show_status($row, $highestRow);

    $stno = (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),0,15));
    $cinsi = (string) rtrim(substr($objWorksheet->getCellByColumnAndRow(5, $row)->getValue(),0,60));
    
    if($stno=='' and $cinsi != '') {
        $bolum = $cinsi;
        continue;
    }
    if($stno!='')
        $insert_stmt->execute();
}
$insert_stmt->close();
$db->query("COMMIT");
$db->close();

function toCP857($data) {
    return iconv('UTF-8','CP857', $data);
}

function toUTF8($data) {
    return iconv('CP857','UTF-8', $data);
}

?>
