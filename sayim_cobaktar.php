<?php
set_time_limit(0);
date_default_timezone_set('Europe/Istanbul');
include 'PHPExcel/IOFactory.php';
include 'RMReader.php';
include 'status.php';
require_once 'linklist.class.php';

$yil = $argv[1];
if($yil == '' || $yil == null) 
	die('Lüften aktarmak istediðiniz yýlý parametre olarak veriniz !!!');
$pasdata = RMReader::read('DATA\\PASSTOK.DAT');
show_status(1, 12);

$stokdata = RMReader::read('DATA\\STOK.DAT');
show_status(2, 12);

$sayimdata = RMReader::read('DATA\\SAYIM.DAT');
show_status(3, 12);


$db = new mysqli('192.168.1.2:3306', 'root', 'root', 'fikopar');

if($db->connect_errno > 0) 
    die("Mysqli hatasý : " . $db->connect_error . "\n");


if(!$db->multi_query("
drop table if exists tmp_sayim;
create TEMPORARY table tmp_sayim (yil int, id int, katno int, stno varchar(255), prcno varchar(255), oemno varchar(255), tipi varchar(255),
		cinsi varchar(255), marka varchar(255), rafno varchar(255), kdv decimal(19,4), adet decimal(19,4), maliyet decimal(19,4), sayimzamani varchar(8));
alter table tmp_sayim add index idx_katno_stno (katno,stno);
insert into tmp_sayim 
	select a.yil,b.id,b.katno,b.stno,b.prcno,b.oemno,b.tipi,b.cinsi,b.marka,b.rafno,b.kdv,b.adet,b.maliyet,b.sayimzamani from windows.win_sayim a, windows.win_sayimdetay b
	where a.id = b.idsayim and a.yil = $yil and trim(b.stno) <> '';
drop table if exists tmp_sayim_duzelt;
create TEMPORARY table tmp_sayim_duzelt(tip int, evrakno varchar(15), katno int, stno varchar(15), sira int, tarih varchar(8), adet decimal(19,4));
insert into tmp_sayim_duzelt select b.`HS-TIP` as tip, b.`HS-EVRAKNO` as evrakno, c.`HD-KATNO` as katno, c.`HD-STNO` as stno, c.`HD-SIRA` as sira, b.`HS-TARIH` as tarih, c.`HD-ADET` as adet from tmp_sayim a
		join cobol.`COB_HAREKET-SICIL` b on b.`HS-TIP`in(1,2,4,5) and 
		  ((a.sayimzamani >= b.`HS-TARIH` and b.`HS-TARIH` >= concat(a.yil,'0101')) or (a.sayimzamani <= b.`HS-TARIH` and b.`HS-TARIH` < concat(a.yil,'0101')))
		join cobol.`COB_HAREKET-DETAY` c on c.`HD-TIP` = b.`HS-TIP` and c.`HD-EVRAKNO` = b.`HS-EVRAKNO` and c.`HD-KATNO` = a.katno and c.`HD-STNO` = a.stno
group by tip, evrakno, katno, stno, sira, tarih, adet;
"))
	die("Mysql hatasý :" . $db->error . "\n");
while ($db->next_result()) {;} 

show_status(4, 12);
		
if(!$result = $db->query("select yil, id, katno, stno, prcno, oemno, tipi, cinsi, marka, rafno, kdv, adet, maliyet, sayimzamani from tmp_sayim order by sayimzamani")) 
	die("zzzzMysql hatasý :" . $db->error . "\n");

  
$sql_sayimdata = Array();
$sayimmap = Array();
while($row = $result->fetch_assoc()) {
	$sql_sayimdata[$row['id']] = Array ("id" => $row['id'], "yil" => $row['yil'], "sayimzamani" => $row['sayimzamani'], "katno" => $row['katno'], "stno" => $row['stno'],
			"prcno" => $row['prcno'], "oemno" => $row['oemno'], "tipi" => $row['tipi'], "cinsi" => $row['cinsi'], "marka" => $row['marka'], "rafno" => $row['rafno'],
			"kdv" => $row['kdv'], "adet" => $row['adet'], "maliyet" => $row['maliyet']);
	if(strlen(trim($row['sayimzamani'])) != 8)
		die("$row[id], $row[katno], $row[stno] ait satýrdaki sayim tarihi hatalý !!!");
	$key = sprintf("%4s%-15s", str_pad($row['katno'],4,'0',STR_PAD_LEFT), $row['stno']);
	if(isset($sayimmap[$key]))
		$sayimmap[$key]->insertLast($row['id']);
	else {
		$sayimmap[$key] = new LinkList();
		$sayimmap[$key]->insertFirst($row['id']);
	}
}
$result->free();

show_status(5, 12);
if(!$result = $db->query("select tip, evrakno, katno, stno, sira, tarih, adet from tmp_sayim_duzelt")) 
	die("Mysql hatasý :" . $db->error . "\n");


$evrakdata = Array();
while($row = $result->fetch_assoc()) {
	$evrakdata[count($evrakdata)] = Array("tip" => $row['tip'], "evrakno" => $row['evrakno'], "katno" => $row['katno'], "stno" => $row['stno'],
							"sira" => $row['sira'], "tarih" => $row['tarih'], "adet" => $row['adet']);
}
$result->free();
show_status(6, 12);


if(!$db->multi_query("
drop table if exists tmp_sayim_duzelt;
drop table if exists tmp_sayim;
"))
	die("Mysql hatasý :" . $db->error . "\n");
while ($db->next_result()) {;} 
$db->close();

show_status(7, 12);

$yilbasi = sprintf("%4s0101", str_pad($yil,4,'0',STR_PAD_LEFT));
/*
 * tarih eðer sayim tarihinden büyükeþitse ve tarih yýl baþýndan küçükse giriþleri ekle
 * tarih eðer sayim tarihinden küçüleþitse ve tarih yýl baþýndan büyükse çýkýþlarý ekle
 */
foreach($evrakdata as $x => $eval) {
	
	$key = sprintf("%4s%-15s", str_pad($eval['katno'],4,'0',STR_PAD_LEFT), $eval['stno']);
				
	if(isset($sayimmap[$key])) {
		for($i = 1; $i<=$sayimmap[$key]->totalNodes(); $i++) {
			
			$id = $sayimmap[$key]->readNode($i);
			$sval = $sql_sayimdata[$id];
			
			if( ($sval['sayimzamani'] <= $eval['tarih'] && $eval['tarih'] < $yilbasi) ||
				($sval['sayimzamani'] >= $eval['tarih'] && $eval['tarih'] >= $yilbasi) ) {
				if( (($eval['tip'] == 4 || $eval['tip'] == 5) && ($sval['sayimzamani'] <= $eval['tarih'] && $eval['tarih'] < $yilbasi)) || 
				     (($eval['tip'] == 1 || $eval['tip'] == 2) && ($sval['sayimzamani'] >= $eval['tarih'] && $eval['tarih'] >= $yilbasi)) ){
					$sval['adet'] += $eval['adet'];
					$sql_sayimdata[$id]['adet'] = $sval['adet'];
					unset($evrakdata[$x]);
				}
				break;
			} 
		}
	} else
		die("(Eklemelerde) $id, $eval[katno], $eval[stno], $eval[adet], $eval[tarih] ait giris bulunamadi!!!\n");
}

show_status(8, 12);
/*
 * tarih eðer sayim tarihinden büyükeþitse ve tarih yýl baþýndan küçükse çýkýþlarý çýkar
 * tarih eðer sayim tarihinden küçüleþitse ve tarih yýl baþýndan büyükse giriþleri çýkar
 */
foreach($evrakdata as $x => $eval) {
	
	$key = sprintf("%4s%-15s", str_pad($eval['katno'],4,'0',STR_PAD_LEFT), $eval['stno']);
	
	if(isset($sayimmap[$key])) {
		for($i = 1; $i<=$sayimmap[$key]->totalNodes(); $i++) {
			
			$id = $sayimmap[$key]->readNode($i);
			if(!isset($sql_sayimdata[$id]))
				die("$id, $eval[katno], $eval[stno], $eval[adet], $eval[tarih] sayim tablosunda bulunamadi unset edildi kontrol et!\n");
			$sval = $sql_sayimdata[$id];
			
			if( ($sval['sayimzamani'] <= $eval['tarih'] && $eval['tarih'] < $yilbasi) ||
				($sval['sayimzamani'] >= $eval['tarih'] && $eval['tarih'] >= $yilbasi) ) {
				if( (($eval['tip'] == 1 || $eval['tip'] == 2) && ($sval['sayimzamani'] <= $eval['tarih'] && $eval['tarih'] < $yilbasi)) || 
				     (($eval['tip'] == 4 || $eval['tip'] == 5) && ($sval['sayimzamani'] >= $eval['tarih'] && $eval['tarih'] >= $yilbasi)) ){
					if($sval['adet'] < $eval['adet']) {
						$eval['adet'] -= $sval['adet'];
						$evrakdata[$x]['adet'] = $eval['adet']; // adres e yazma direk böyleymiþ SAÇMA!!!
						unset($sql_sayimdata[$id]);
						continue;
					} else {
						$sval['adet'] -= $eval['adet'];
						$sql_sayimdata[$id]['adet'] = $sval['adet'];
						if($sval['adet'] == 0) {
							unset($sql_sayimdata[$id]);
							$sayimmap[$key]->deleteNode($id);
						}
						unset($evrakdata[$x]);
					}
				}
				break;
			} 
		}
	} else
		die("(Eklemelerde) $id, $eval[katno], $eval[stno], $eval[adet], $eval[tarih] ait giris bulunamadi!!!\n");
}

show_status(9, 12);

$outFile = fopen("c:\\windows\\temp\\sayim.tmp", 'w');
foreach($sql_sayimdata as $k => $v) {
    $key = sprintf("%4s%4s%-15s%10s", toCP857($v['yil']),
                    str_pad($v['katno'],4,'0',STR_PAD_LEFT),
                    toCP857(substr($v['stno'],0,15)), 
                    str_pad($v['id'],10,'0',STR_PAD_LEFT));
    $stokkey = sprintf("%4s%-15s", str_pad($v['katno'],4,'0',STR_PAD_LEFT),
                    toCP857(substr($v['stno'],0,15)));
    $val = sprintf("%4s%4s%-15s%10s%4s%-30s%-30s%-30s%-60s%-30s%19s%14s%9s%-15s",
                    toCP857($v['yil']),
                    str_pad($v['katno'],4,'0',STR_PAD_LEFT),
                    toCP857(substr($v['stno'],0,15)), 
                    str_pad($v['id'],10,'0',STR_PAD_LEFT),
                    $v['kdv']==0 ? '0002' : '0001',
                    toCP857(substr($v['prcno'],0,30)),
                    toCP857(substr($v['oemno'],0,30)),
                    toCP857(substr($v['tipi'],0,30)),
                    toCP857(substr($v['cinsi'],0,60)),
                    toCP857(substr($v['marka'],0,60)),
                    str_pad(number_format($v['maliyet'], 4,'',''), 19, '0', STR_PAD_LEFT),
                    str_pad(number_format($v['adet'],4,'',''), 14, '0', STR_PAD_LEFT),
                    str_pad(number_format($v['kdv'],4,'',''), 9, '0', STR_PAD_LEFT),
                    toCP857(substr($v['rafno'],0,15)));
    if(!isset($pasdata[$stokkey]) and !isset($stokdata[$stokkey])) {
        fwrite(STDERR, "\n$v[stno] stok veya passtok tablosunda bulunmuyor ! \n" . PHP_EOL);
        continue;
    }
    
    if(isset($sayimdata[$key])) {
        if($sayimdata[$key] == $val) {
            unset($sayimdata[$key]);
            continue;
        } else {
            fwrite($outFile, "2" . $val . "\n");
            unset($sayimdata[$key]);
        }
    } else
        fwrite($outFile, "1" . $val . "\n" );
}
show_status(10, 12);

foreach($sayimdata as $dat) {
	if(substr($dat,0,4) == $yil)
		fwrite($outFile, "0" . $dat . "\n" );
}
fclose($outFile);

show_status(11, 12);

$WshShell = new COM("WScript.Shell"); 
$oExec = $WshShell->Run('"C:\Program Files\Liant\RMCOBOLv11\runcobol.exe" SAYIMAKT L=Z:\FIKOPAR\mysql.dll A="C:\WINDOWS\TEMP\SAYIM.TMP"', 7, true); 

show_status(12, 12);

function toCP857($data) {
    return iconv('UTF-8','CP857', $data);
}

?>
