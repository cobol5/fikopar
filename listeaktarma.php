<?php
set_time_limit(0);
ini_set('memory_limit', '-1');

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
function execute($cmd) {
    $proc = proc_open($cmd, [['pipe','r'],['pipe','w'],['pipe','w']], $pipes);
    while(($line = fgets($pipes[1])) !== false) {
        fwrite(STDOUT,$line);
    }
    while(($line = fgets($pipes[2])) !== false) {
        fwrite(STDERR,$line);
    }
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    return proc_close($proc);
}
function isConnectionActive() {
	if(!$link = mysqli_connect("***remotedns****", "root", "root")) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
	    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	    exit;
	}
	$link->query("SET NAMES 'utf8'");
	$link->query("SET CHARACTER SET utf8_turkish_ci");

	if(!$stmt = $link->query("select active from dizeltarim.wb_connection where id = 2")) {
		echo "Error create select statement [" . $link->error . "]" . PHP_EOL;
		exit();	
	}
	$row = $stmt->fetch_assoc();
	$link->close();
	if($row['active'] == "1")
		return true;
	return false;
}
$listepath = iconv("UTF-8","ISO-8859-9","\\\\192.168.1.2\\Fiyat Listeleri\\DİZEL TARIM FİYAT LİSTESİ.xls");
$sheet_massey_uzel = iconv("UTF-8","ISO-8859-9", "MASSEY & UZEL"); // 0001
$sheet_newholland = iconv("UTF-8","ISO-8859-9", "NEW HOLLAND"); //0023
$sheet_ford = iconv("UTF-8","ISO-8859-9", "FORD"); //0024
$sheet_steyr = iconv("UTF-8","ISO-8859-9", "STEYR"); //0025
$sheet_tumosan = iconv("UTF-8","ISO-8859-9", "TÜMOSAN"); //0026
$sheet_erkunt = iconv("UTF-8","ISO-8859-9", "ERKUNT"); //0027
$sheet_hattat_valtra = iconv("UTF-8","ISO-8859-9", "HATTAT-VALTRA"); //0028
$sheet_same = iconv("UTF-8","ISO-8859-9", "SAME"); //0029
$sheet_ismakinasi_jenerator = iconv("UTF-8","ISO-8859-9", "İŞ MAKİNASI & JENERATÖR"); //0030

$processes = Array ( 
    Array ( "fileName" => "DATA\\KATLOG.DAT",
            "lastModified" => 0,
            "command" => "katlog_sqlaktar.php" ),
    Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0001\" \"$listepath\" \"$sheet_massey_uzel\" 0 0 6 1"),
	Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0023\" \"$listepath\" \"$sheet_newholland\" 0 0 6 1"),
	Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0024\" \"$listepath\" \"$sheet_ford\" 0 0 6 1"),
	Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0025\" \"$listepath\" \"$sheet_steyr\" 0 0 6 1"),
	Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0026\" \"$listepath\" \"$sheet_tumosan\" 0 0 6 1"),
	Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0027\" \"$listepath\" \"$sheet_erkunt\" 0 0 6 1"),
	Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0028\" \"$listepath\" \"$sheet_hattat_valtra\" 0 0 6 1"),
	Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0029\" \"$listepath\" \"$sheet_same\" 0 0 6 1"),
	Array ( "fileName" => $listepath,
            "lastModified" => 0,
            "command" => "listeaktar.php \"0030\" \"$listepath\" \"$sheet_ismakinasi_jenerator\" 0 0 6 1"),
    Array ( "fileName" => "DATA\\STOKBAG.DAT",
            "lastModified" => 0,
            "command" => "stokbag_sqlaktar.php" ),
    Array ( "fileName" => "DATA\\STOK.DAT",
            "lastModified" => 0,
            "command" => "stok_sqlaktar.php" ));
while(1) {
	while(isConnectionActive()) {
		echo "Veri aktariliyor ... bitmesi bekleniyor ...\n";
		usleep(1000*10000);	
	}
	if(!$link = mysqli_connect("***remotedns****", "root", "root")) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
	    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	    exit;
	}
	$link->query("SET NAMES 'utf8'");
	$link->query("SET CHARACTER SET utf8_turkish_ci");
	$link->query("update dizeltarim.wb_connection set message = DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y%m%d%H%i%s'), active = 1 where id = 4;");
	$link->query("COMMIT");

    foreach($processes as $key => $process) {
        if(file_exists($process['fileName'])) {
            $lastModified = filemtime($process['fileName']);
            echo "$process[fileName] last modified $lastModified == $process[lastModified] \n";
            if($lastModified != $process['lastModified']) {
                start_time();
                echo "INFO : ($process[command]) Started. \n";
                execute("C:\\php\\php.exe $process[command]");
                end_time("INFO : ($process[command]) Done");
                $process['lastModified'] = $lastModified;
                $processes[$key] = $process;
            } else {
               echo "INFO : $process[fileName] does not change.\n";
            }
        } else {
            echo "ERROR : $process[fileName] does not exists !!!\n";
        }
        echo "\n";
    }
	$link->query("update dizeltarim.wb_connection set active = 0 where id = 4;");
	$link->query("COMMIT");
	$link->close();
    start_time();
    echo "INFO : Program going to sleep ... ";
    usleep(1000*10000);
    end_time("end sleep");
}
?>