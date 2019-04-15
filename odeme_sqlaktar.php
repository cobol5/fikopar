<?php
set_time_limit(0);
include 'RMReader.php';
include 'status.php';

$data = RMReader::read('DATA\\ODEME.DAT');
$data_size = count($data);

$db = new mysqli('192.168.1.2:3306', 'root', 'root', 'fikopar');

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

$sql = "CREATE TABLE IF NOT EXISTS ODEME (
        sekilno int(4),
        sekiladi varchar(60),
        PRIMARY KEY (sekilno)
    );";

if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
}

$dbex = new mysqli('192.168.1.2:3306', 'root', 'root', 'fikopar');

$update_query = "update odeme set sekilno = ?, sekiladi = ? where sekilno = ?";
$insert_query = "insert into odeme values (?,?)";

$delete_query = "delete from odeme where sekilno = ?";

if(!$update_stmt = $dbex->prepare($update_query))
    die("Error create update statement [" . $dbex->error . "]");

$update_stmt->bind_param("isi", $sekilno, $sekiladi, $sekilno);

if(!$insert_stmt = $dbex->prepare($insert_query))
    die("Error create insert statement [" . $dbex->error . "]");

$insert_stmt->bind_param("is", $sekilno, $sekiladi);

if(!$delete_stmt = $dbex->prepare($delete_query))
    die("Error create delete statement [" . $dbex->error . "]");
    

$delete_stmt->bind_param("i", $dpno);

$dbex->query("START TRANSACTION");

$sql = 'select * from odeme';

if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
}
while($row = $result->fetch_assoc()){
     $key = sprintf("%4s", str_pad(number_format($row['sekilno'],0,'',''),4,'0',STR_PAD_LEFT));
     $val = sprintf("%4s%-60s", str_pad(number_format($row['sekilno'],0,'',''),4,'0',STR_PAD_LEFT), toCP857($row['sekiladi']));
     
     if(isset($data[$key])) {
         if($data[$key] == $val) {
           unset($data[$key]);
           show_status($data_size - count($data), $data_size);
           continue;
         } else {
           $sekilno = floatVal(substr($data[$key],0,4));
           $sekiladi = (string) rtrim(toUTF8(substr($data[$key],4,60)));
           $update_stmt->execute();
           unset($data[$key]);
           show_status($data_size - count($data), $data_size);
         }
     } else {
         $sekilno = $row['sekilno'];
         $delete_stmt->execute();
     }
}

foreach($data as $key => $val) {
   $sekilno = floatVal(substr($data[$key],0,4));
   $sekiladi = (string) rtrim(toUTF8(substr($data[$key],4,60)));
   $insert_stmt->execute();
   unset($data[$key]);           
   show_status($data_size - count($data), $data_size);
}
$update_stmt->close();
$insert_stmt->close();
$delete_stmt->close();
$dbex->query("COMMIT");
$dbex->close();
$db->close();


function toCP857($data) {
    return iconv('UTF-8','CP857', $data);
}
function toUTF8($data) {
    return iconv('CP857','UTF-8', $data);
}
?>
