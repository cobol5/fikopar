drop table if exists `COB_USERS`;
create table `COB_USERS` (
	id bigint unsigned not null auto_increment,
	`US-NAME` VARCHAR(20) NOT NULL,
	`US-PASS` VARCHAR(20) NOT NULL,
	`US-ACTIVE` DECIMAL(1,0) NOT NULL,
	primary key (id),
	INDEX `COB_KEY` (`US-NAME`));

drop table if exists `COB_USERACCESS`;
create table `COB_USERACCESS` (
	id bigint unsigned not null auto_increment,
	`UA-NAME` VARCHAR(20) NOT NULL,
	`UA-CODE` VARCHAR(20) NOT NULL,
	`UA-READ` DECIMAL(1,0) NOT NULL,
	`UA-WRITE` DECIMAL(1,0) NOT NULL,
	`UA-UPDATE` DECIMAL(1,0) NOT NULL,
	`UA-DELETE` DECIMAL(1,0) NOT NULL,
	primary key (id),
	INDEX `COB_KEY` (`UA-NAME`,`UA-CODE`));
	   
drop table if exists `COB_TIPKOD`;
create table `COB_TIPKOD` (
	id bigint unsigned not null auto_increment,
	`TK-KOD` DECIMAL(4,0) NOT NULL,
	`TK-TIP` VARCHAR(40) NOT NULL,
	primary key (id),
	INDEX `COB_KEY` (`TK-KOD`));

drop table if exists `COB_DTIP`;
create table `COB_DTIP` (
	id bigint unsigned not null auto_increment,
	`DTIP-NO` DECIMAL(2,0) NOT NULL,
	`DTIP-ADI` VARCHAR(60) NOT NULL,
	primary key (id),
	INDEX `COB_KEY` (`DTIP-NO`));

drop table if exists `COB_STOK`;
create table `COB_STOK` (
	id bigint unsigned not null auto_increment,
	`ST-KATNO` DECIMAL(4,0) NOT NULL, 
	`ST-STNO` VARCHAR(15) NOT NULL, 
	`ST-PRCNO` VARCHAR(30) NOT NULL, 
	`ST-OEMNO` VARCHAR(30) NOT NULL, 
	`ST-TIPI` VARCHAR(30) NOT NULL, 
	`ST-CINSI` VARCHAR(60) NOT NULL, 
	`ST-MARKA` VARCHAR(30) NOT NULL, 
	`ST-MIN` DECIMAL(16,4) NOT NULL, 
	`ST-MAX` DECIMAL(16,4) NOT NULL, 
	`ST-FIYAT` DECIMAL(14,4) NOT NULL,
	`ST-NOTE` VARCHAR(10) NOT NULL, 
	`ST-DEG` VARCHAR(8) NOT NULL, 
	`ST-PAKET` DECIMAL(5,0) NOT NULL, 
	`ST-SIRA` DECIMAL(15,0)  NOT NULL,
	primary key (id),
	INDEX `COB_KEY` (`ST-KATNO`, `ST-STNO`));
	
drop table if exists `COB_STOKBAG`;
create table `COB_STOKBAG` (
	id bigint unsigned not null auto_increment,
	`SB-KATNO` DECIMAL(4,0) NOT NULL, 
	`SB-STNO` VARCHAR(15) NOT NULL, 
	`SB-SIRA` DECIMAL(12,0) NOT NULL, 
	`SB-BAG-KATNO` DECIMAL(4,0) NOT NULL, 
	`SB-BAG-STNO` VARCHAR(15) NOT NULL, 
	`SB-ISK` DECIMAL(9,4) NOT NULL, 
	`ST-KDV` DECIMAL(9,4) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`SB-KATNO`, `SB-STNO`, `SB-SIRA`));
           
drop table if exists `COB_PASSTOK`;
create table `COB_PASSTOK` (
	id bigint unsigned not null auto_increment,
	`PAS-KATNO` DECIMAL(4,0) NOT NULL, 
	`PAS-STNO` VARCHAR(15) NOT NULL, 
	`PAS-PRCNO` VARCHAR(30) NOT NULL, 
	`PAS-OEMNO` VARCHAR(30) NOT NULL, 
	`PAS-TIPI` VARCHAR(30) NOT NULL, 
	`PAS-CINSI` VARCHAR(60) NOT NULL, 
	`PAS-MARKA` VARCHAR(30) NOT NULL, 
	`PAS-MIN` DECIMAL(16,4) NOT NULL, 
	`PAS-MAX` DECIMAL(16,4) NOT NULL, 
	`PAS-FIYAT` DECIMAL(14,4) NOT NULL,
	`PAS-NOTE` VARCHAR(10) NOT NULL, 
	`PAS-DEG` VARCHAR(8) NOT NULL, 
	`PAS-PAKET` DECIMAL(5,0) NOT NULL, 
	`PAS-TARIH` VARCHAR(8) NOT NULL, 
	`PAS-ZAMAN` VARCHAR(6) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`PAS-KATNO`, `PAS-STNO`));
	
drop table if exists `COB_STOKADET`;
create table `COB_STOKADET` (
	id bigint unsigned not null auto_increment,
	`STA-KATNO` DECIMAL(4,0) NOT NULL, 
	`STA-STNO` VARCHAR(15) NOT NULL, 
	`STA-YIL` DECIMAL(4,0) NOT NULL, 
	`STA-DEPONO` DECIMAL(4,0) NOT NULL, 
	`STA-GIR` DECIMAL(16,4) NOT NULL, 
	`STA-CIK` DECIMAL(16,4) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`STA-KATNO`, `STA-STNO`, `STA-YIL`, `STA-DEPONO`));

drop table if exists `COB_SICILTEL`;
create table `COB_SICILTEL` (
	id bigint unsigned not null auto_increment,
	`SCT-HESAPNO` VARCHAR(15) NOT NULL, 
	`SCT-SIRA` DECIMAL(10,0) NOT NULL, 
	`SCT-TEL` DECIMAL(12,0) NOT NULL, 
	`SCT-DAHILI` VARCHAR(5) NOT NULL, 
	`SCT-TIP` DECIMAL(1,0) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`SCT-HESAPNO`, `SCT-SIRA`));

drop table if exists `COB_SICILPOS`;
create table `COB_SICILPOS` (
	id bigint unsigned not null auto_increment,
	`SP-HESAPNO` VARCHAR(15) NOT NULL, 
	`SP-SIRA` DECIMAL(10,0) NOT NULL, 
	`SP-MAIL` VARCHAR(65) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`SP-HESAPNO`, `SP-SIRA`));

drop table if exists `COB_SICILKOD`;
create table `COB_SICILKOD` (
	id bigint unsigned not null auto_increment,
	`SK-HESAPNO` VARCHAR(15) NOT NULL, 
	`SK-KOD` DECIMAL(4,0) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`SK-HESAPNO`, `SK-KOD`));

drop table if exists `COB_SICIL`;
create table `COB_SICIL` (
	id bigint unsigned not null auto_increment,
	`SC-HESAPNO` VARCHAR(15) NOT NULL, 
	`SC-HESAPADI` VARCHAR(50) NOT NULL, 
	`SC-YETKILI` VARCHAR(40) NOT NULL, 
	`SC-ADRES1` VARCHAR(40) NOT NULL, 
	`SC-ADRES2` VARCHAR(40) NOT NULL, 
	`SC-ADRES3` VARCHAR(40) NOT NULL, 
	`SC-POSKOD` VARCHAR(6) NOT NULL, 
	`SC-MAHALLE` VARCHAR(20) NOT NULL, 
	`SC-ILCE` VARCHAR(20) NOT NULL, 
	`SC-IL` VARCHAR(20) NOT NULL, 
	`SC-ULKE` VARCHAR(20) NOT NULL, 
	`SC-VDA` VARCHAR(30) NOT NULL, 
	`SC-VNO` VARCHAR(10) NOT NULL, 
	`SC-TCNO` VARCHAR(11) NOT NULL, 
	`SC-TSNO` VARCHAR(11) NOT NULL, 
	`SC-MERSIS` VARCHAR(20) NOT NULL, 
	`SC-TUR` VARCHAR(3) NOT NULL, 
	`SC-KREDI` DECIMAL(18,4) NOT NULL, 
	`SC-GUN` DECIMAL(5,0) NOT NULL, 
	`SC-ISKONTO1` DECIMAL(8,4) NOT NULL, 
	`SC-ISKONTO2` DECIMAL(8,4) NOT NULL, 
	`SC-PLKOD` DECIMAL(4,0) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`SC-HESAPNO`));
	           
drop table if exists `COB_SAYIM`;
create table `COB_SAYIM` (
	id bigint unsigned not null auto_increment,
	`SY-YIL` DECIMAL(4,0) NOT NULL, 
	`SY-KATNO` DECIMAL(4,0) NOT NULL,
	`SY-STNO` VARCHAR(15) NOT NULL, 
	`SY-SIRA` DECIMAL(10,0) NOT NULL,
	`SY-DEPONO` DECIMAL(4,0) NOT NULL,
	`SY-PRCNO` VARCHAR(30) NOT NULL, 
	`SY-OEMNO` VARCHAR(30) NOT NULL, 
	`SY-TIPI` VARCHAR(30) NOT NULL, 
	`SY-CINSI` VARCHAR(60) NOT NULL, 
	`SY-MARKA` VARCHAR(30) NOT NULL, 
	`SY-FIYAT` DECIMAL(19,4) NOT NULL,
	`SY-ADET` DECIMAL(14,4) NOT NULL,
	`SY-KDV` DECIMAL(9,4) NOT NULL,
	`SY-RAF` VARCHAR(15) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`SY-YIL`, `SY-KATNO`, `SY-STNO`, `SY-SIRA`));

drop table if exists `COB_PLASIYER`;
create table `COB_PLASIYER` (
	id bigint unsigned not null auto_increment,
	`PL-NO` DECIMAL(4,0) NOT NULL, 
	`PL-ADI` VARCHAR(60) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`PL-NO`));

drop table if exists `COB_ODEME`;
create table `COB_ODEME` (
	id bigint unsigned not null auto_increment,
	`OD-NO` DECIMAL(4,0) NOT NULL, 
	`OD-ADI` VARCHAR(60) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`OD-NO`));

drop table if exists `COB_MAKBUZ`;
create table `COB_MAKBUZ` (
	id bigint unsigned not null auto_increment,
	`MK-EVRAKNO` VARCHAR(15) NOT NULL, 
	`MK-TIP` DECIMAL(2,0) NOT NULL, 
	`MK-SEKILNO` DECIMAL(4,0) NOT NULL, 
	`MK-HESAPNO` VARCHAR(15) NOT NULL, 
	`MK-DEPONO` DECIMAL(4,0) NOT NULL, 
	`MK-TARIH` VARCHAR(8) NOT NULL, 
	`MK-ACIKLAMA` VARCHAR(60) NOT NULL,
	`MK-TUTAR` DECIMAL(15,2) NOT NULL, 	
	primary key (id),
	INDEX `COB_KEY` (`MK-EVRAKNO`,`MK-TIP`));

drop table if exists `COB_KATLOG`;
create table `COB_KATLOG` (
	id bigint unsigned not null auto_increment,
	`KT-KATNO` DECIMAL(4,0) NOT NULL, 
	`KT-ACIKLAMA` VARCHAR(60) NOT NULL, 
	`KT-HESAPNO` VARCHAR(15) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`KT-KATNO`));

drop table if exists `COB_KASA`;
create table `COB_KASA` (
	id bigint unsigned not null auto_increment,
	`KS-TARIH` VARCHAR(8) NOT NULL, 
	`KS-SIRA` DECIMAL(5,0) NOT NULL, 
	`KS-HESAPNO` VARCHAR(15) NOT NULL, 
	`KS-ACIKLAMA` VARCHAR(60) NOT NULL,
	`KS-GELIR` DECIMAL(15,2) NOT NULL, 	
	`KS-GIDER` DECIMAL(15,2) NOT NULL,
	`KS-MTIP` DECIMAL(2,0) NOT NULL, 	
	`KS-MEVRAKNO` VARCHAR(15) NOT NULL, 	
	primary key (id),
	INDEX `COB_KEY` (`KS-TARIH`,`KS-SIRA`));

drop table if exists `COB_HAREKET-SICIL`;
create table `COB_HAREKET-SICIL` (
	id bigint unsigned not null auto_increment,
	`HS-TIP` DECIMAL(2,0) NOT NULL, 
	`HS-EVRAKNO` VARCHAR(15) NOT NULL, 
	`HS-TARIH` VARCHAR(8) NOT NULL, 
	`HS-SAAT` VARCHAR(6) NOT NULL, 
	`HS-FTARIH` VARCHAR(8) NOT NULL, 
	`HS-HESAPNO` VARCHAR(15) NOT NULL, 
	`HS-HESAPADI` VARCHAR(50) NOT NULL, 
	`HS-YETKILI` VARCHAR(40) NOT NULL, 
	`HS-ADRES1` VARCHAR(40) NOT NULL, 
	`HS-ADRES2` VARCHAR(40) NOT NULL, 
	`HS-ADRES3` VARCHAR(40) NOT NULL, 
	`HS-MAHALLE` VARCHAR(20) NOT NULL, 
	`HS-ILCE` VARCHAR(20) NOT NULL, 
	`HS-IL` VARCHAR(20) NOT NULL, 
	`HS-ULKE` VARCHAR(20) NOT NULL, 
	`HS-VDA` VARCHAR(30) NOT NULL, 
	`HS-VNO` VARCHAR(10) NOT NULL, 
	`HS-TCNO` VARCHAR(11) NOT NULL, 
	`HS-TSNO` VARCHAR(11) NOT NULL, 
	`HS-MERSIS` VARCHAR(20) NOT NULL, 
	`HS-GUN` DECIMAL(5,0) NOT NULL, 
	`HS-PLKOD` DECIMAL(4,0) NOT NULL, 
	`HS-DEPONO` DECIMAL(4,0) NOT NULL, 
	`HS-DUZEN` DECIMAL(4,0) NOT NULL, 
	`HS-BAGTIPI1` DECIMAL(2,0) NOT NULL, 
	`HS-BEVRAKNO1` VARCHAR(15) NOT NULL, 
	`HS-BAGTIPI2` DECIMAL(2,0) NOT NULL, 
	`HS-BEVRAKNO2` VARCHAR(15) NOT NULL, 
	`HS-SONUC` DECIMAL(2,0) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`HS-TIP`,`HS-EVRAKNO`));

drop table if exists `COB_HAREKET-DETAY`;
create table `COB_HAREKET-DETAY` (
	id bigint unsigned not null auto_increment,
	`HD-TIP` DECIMAL(2,0) NOT NULL, 
	`HD-EVRAKNO` VARCHAR(15) NOT NULL, 
	`HD-SIRA` DECIMAL(10,0) NOT NULL, 
	`HD-KATNO` DECIMAL(4,0) NOT NULL, 
	`HD-STNO` VARCHAR(15) NOT NULL, 
	`HD-PRCNO` VARCHAR(30) NOT NULL, 
	`HD-TIPI` VARCHAR(30) NOT NULL, 
	`HD-CINSI` VARCHAR(60) NOT NULL, 
	`HD-MARKA` VARCHAR(30) NOT NULL, 
	`HD-ISKONTO1` DECIMAL(8,4) NOT NULL, 
	`HD-ISKONTO2` DECIMAL(8,4) NOT NULL, 
	`HD-KDV` DECIMAL(8,4) NOT NULL, 
	`HD-ADET` DECIMAL(16,4) NOT NULL, 
	`HD-FIYAT` DECIMAL(14,4) NOT NULL,
	primary key (id),
	INDEX `COB_KEY` (`HD-TIP`, `HD-EVRAKNO`, `HD-SIRA`));
       
drop table if exists `COB_DEPO`;
create table `COB_DEPO` (
	id bigint unsigned not null auto_increment,
	`DP-NO` DECIMAL(4,0) NOT NULL,
	`DP-ADI` VARCHAR(30) NOT NULL,
	`DP-KDV` DECIMAL(8,4) NOT NULL,
	primary key (id),
	INDEX `COB_KEY` (`DP-NO`));
	   
drop table if exists `COB_CEKSENET`;
create table `COB_CEKSENET` (
	id bigint unsigned not null auto_increment,
	`CS-EVRAKNO` VARCHAR(15) NOT NULL, 
	`CS-TIP` DECIMAL(2,0) NOT NULL, 
	`CS-HESAPNO` VARCHAR(15) NOT NULL,
	`CS-DEPONO` DECIMAL(4,0) NOT NULL, 
	`CS-VADE` VARCHAR(8) NOT NULL, 
	`CS-TARIH` VARCHAR(8) NOT NULL, 
	`CS-TUTAR` DECIMAL(15,2) NOT NULL, 
	`CS-KHESAPNO` VARCHAR(15) NOT NULL, 
	`CS-KHESAPADI` VARCHAR(50) NOT NULL, 
	`CS-KYETKILI` VARCHAR(40) NOT NULL, 
	`CS-KBANKASEHIR` VARCHAR(20) NOT NULL, 
	`CS-KSUBEILCE` VARCHAR(20) NOT NULL, 
	`CS-KBANKANOTCNO` VARCHAR(20) NOT NULL, 
	`CS-KCEKSENETNO` VARCHAR(20) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`CS-EVRAKNO`, `CS-TIP`));
	   
drop table if exists `COB_CEKSENETLOG`;
create table `COB_CEKSENETLOG` (
	id bigint unsigned not null auto_increment,
	`CSL-EVRAKNO` VARCHAR(15) NOT NULL, 
	`CSL-CTIP` DECIMAL(2,0) NOT NULL, 
	`CSL-SIRA` DECIMAL(10,0) NOT NULL, 
	`CSL-TARIH` VARCHAR(8) NOT NULL, 
	`CSL-SONUC` DECIMAL(4,0) NOT NULL,
	`CSL-MAKBUZNO` VARCHAR(15) NOT NULL, 
	`CSL-TIP` DECIMAL(2,0) NOT NULL,
	`CSL-OPTIME` DECIMAL(16,0) NOT NULL,
	primary key (id),
	INDEX `COB_KEY` (`CSL-EVRAKNO`, `CSL-CTIP`));
	   
	   
drop table if exists `COB_CARHAR`;
create table `COB_CARHAR` (
	id bigint unsigned not null auto_increment,
	`CH-HESAPNO` VARCHAR(15) NOT NULL, 
	`CH-DEPONO` DECIMAL(4,0) NOT NULL, 
	`CH-TARIH` VARCHAR(8) NOT NULL, 
	`CH-SIRA` DECIMAL(10,0) NOT NULL, 
	`CH-TIP` DECIMAL(2,0) NOT NULL, 
	`CH-EVRAKNO` VARCHAR(15) NOT NULL, 
	`CH-ACIKLAMA` VARCHAR(60) NOT NULL, 
	`CH-VADE` VARCHAR(15) NOT NULL, 
	`CH-BORC` DECIMAL(15,2) NOT NULL, 
	`CH-ALACAK` DECIMAL(15,2) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`CH-HESAPNO`, `CH-DEPONO`, `CH-TARIH`, `CH-SIRA`));

drop table if exists `COB_AYAR`;
create table `COB_AYAR` (
	id bigint unsigned not null auto_increment,
	`AY-TIP` DECIMAL(2,0) NOT NULL, 
	`AY-DEPONO` DECIMAL(4,0) NOT NULL, 
	`AY-EVRAKNO` VARCHAR(15) NOT NULL, 
	`AY-EVRAKFORMAT` VARCHAR(30) NOT NULL, 
	`AY-ACIKLAMA` VARCHAR(60) NOT NULL, 
	`AY-YAZDOS` VARCHAR(12) NOT NULL, 
	`AY-YAZPORT` VARCHAR(12) NOT NULL, 
	primary key (id),
	INDEX `COB_KEY` (`AY-TIP`, `AY-DEPONO`));

drop table if exists `LOG_COBOL`;
create table `LOG_COBOL` (
	id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`TABLENAME` varchar(100) NOT NULL,
	`KEYID` bigint unsigned NOT NULL,
	`OPERATION` ENUM('INSERT','UPDATE','DELETE','TRUNCATE'),
	`OPTIME` varchar(16) NOT NULL,
	PRIMARY KEY (id),
	INDEX `LOG_INDEX` (`TABLENAME`,`OPTIME`) );
	

DELIMITER //

CREATE TRIGGER `trg_insert_cob_hareket_detay` AFTER INSERT ON `cob_hareket-detay`
FOR EACH ROW TheTrigger : BEGIN
  DECLARE v_siparistipi, v_id integer;
  DECLARE v_adet, v_bakiye decimal(20,4);
  DECLARE v_hesapno varchar(15);
  DECLARE v_tarih varchar(8);
  IF (NEW.`HD-TIP` > 5 OR NEW.`HD-TIP` = 3 OR NEW.`HD-STNO` = '') THEN
		leave TheTrigger;
  END IF;
  IF (NEW.`HD-TIP` > 3) THEN
	SET v_siparistipi = 1;
  ELSE
  	SET v_siparistipi = 0;
  END IF;
  SET v_adet = NEW.`HD-ADET`;
  (select a.`HS-TARIH`, a.`HS-HESAPNO` INTO v_tarih, v_hesapno from `COBOL`.`COB_HAREKET-SICIL` a where a.`HS-TIP` = NEW.`HD-TIP` and a.`HS-EVRAKNO` = NEW.`HD-EVRAKNO`);	
	wloop_insert: while v_adet > 0 do
		SET v_id = NULL;
		SET v_bakiye = NULL;
		(select b.id, b.adet-b.sevkadet as bakiye into v_id, v_bakiye 
			from `WINDOWS`.`WIN_SIPARIS` a, `WINDOWS`.`WIN_SIPARISDETAY` b where a.id = b.idsiparis and a.hesapno = v_hesapno and a.siparistipi = v_siparistipi and a.tarih <= v_tarih
						and b.katno = NEW.`HD-KATNO` and b.stno = NEW.`HD-STNO` and b.adet > b.sevkadet order by a.tarih limit 1);
		IF (v_id IS NULL) THEN
			leave wloop_insert;
		END IF;
		IF (v_adet > v_bakiye) THEN
				SET v_adet = v_adet - v_bakiye;
				insert into `WINDOWS`.`WIN_SIPARISTABLO`(idsiparis,tip,evrakno,sira,adet) values (v_id,NEW.`HD-TIP`,NEW.`HD-EVRAKNO`,NEW.`HD-SIRA`,v_bakiye);
				update `WINDOWS`.`WIN_SIPARISDETAY` a set a.sevkadet = a.sevkadet + v_bakiye where a.id = v_id;
		ELSE
				insert into `WINDOWS`.`WIN_SIPARISTABLO`(idsiparis,tip,evrakno,sira,adet) values (v_id,NEW.`HD-TIP`,NEW.`HD-EVRAKNO`,NEW.`HD-SIRA`,v_adet);
				update `WINDOWS`.`WIN_SIPARISDETAY` a set a.sevkadet = a.sevkadet + v_adet where a.id = v_id;
				SET v_adet = 0;
		END IF;
	end while wloop_insert;
END

//

CREATE TRIGGER `trg_update_cob_hareket_detay` AFTER UPDATE ON `cob_hareket-detay`
FOR EACH ROW TheTrigger : BEGIN
  DECLARE v_siparistipi, v_id integer;
  DECLARE v_adet, v_bakiye decimal(20,4);
  DECLARE v_hesapno varchar(15);
  DECLARE v_tarih varchar(8);
  update `WINDOWS`.`WIN_SIPARISDETAY` a, 
		(select x.idsiparis, sum(x.adet)  as adet from `WINDOWS`.`WIN_SIPARISTABLO` x where x.tip = OLD.`HD-TIP` and x.evrakno = OLD.`HD-EVRAKNO` and x.sira = OLD.`HD-SIRA` group by x.idsiparis) b 
		set a.sevkadet = a.sevkadet - b.adet where a.id = b.idsiparis;
  DELETE from `WINDOWS`.`WIN_SIPARISTABLO` where tip = OLD.`HD-TIP` and evrakno = OLD.`HD-EVRAKNO` and sira = OLD.`HD-SIRA`;
  IF (NEW.`HD-TIP` > 5 OR NEW.`HD-TIP` = 3 OR NEW.`HD-STNO` = '') THEN
		leave TheTrigger;
  END IF;
  IF (NEW.`HD-TIP` > 3) THEN
	SET v_siparistipi = 1;
  ELSE
  	SET v_siparistipi = 0;
  END IF;
  SET v_adet = NEW.`HD-ADET`;
  (select a.`HS-TARIH`, a.`HS-HESAPNO` INTO v_tarih, v_hesapno from `COBOL`.`COB_HAREKET-SICIL` a where a.`HS-TIP` = NEW.`HD-TIP` and a.`HS-EVRAKNO` = NEW.`HD-EVRAKNO`);	
	wloop_insert: while v_adet > 0 do
		SET v_id = NULL;
		SET v_bakiye = NULL;
		(select b.id, b.adet-b.sevkadet as bakiye into v_id, v_bakiye 
			from `WINDOWS`.`WIN_SIPARIS` a, `WINDOWS`.`WIN_SIPARISDETAY` b where a.id = b.idsiparis and a.hesapno = v_hesapno and a.siparistipi = v_siparistipi and a.tarih <= v_tarih
						and b.katno = NEW.`HD-KATNO` and b.stno = NEW.`HD-STNO` and b.adet > b.sevkadet order by a.tarih limit 1);
		IF (v_id IS NULL) THEN
			leave wloop_insert;
		END IF;
		IF (v_adet > v_bakiye) THEN
				SET v_adet = v_adet - v_bakiye;
				insert into `WINDOWS`.`WIN_SIPARISTABLO`(idsiparis,tip,evrakno,sira,adet) values (v_id,NEW.`HD-TIP`,NEW.`HD-EVRAKNO`,NEW.`HD-SIRA`,v_bakiye);
				update `WINDOWS`.`WIN_SIPARISDETAY` a set a.sevkadet = a.sevkadet + v_bakiye where a.id = v_id;
		ELSE
				insert into `WINDOWS`.`WIN_SIPARISTABLO`(idsiparis,tip,evrakno,sira,adet) values (v_id,NEW.`HD-TIP`,NEW.`HD-EVRAKNO`,NEW.`HD-SIRA`,v_adet);
				update `WINDOWS`.`WIN_SIPARISDETAY` a set a.sevkadet = a.sevkadet + v_adet where a.id = v_id;
				SET v_adet = 0;
		END IF;
	end while wloop_insert;
END

//

CREATE TRIGGER `trg_delete_cob_hareket_detay` AFTER DELETE ON `cob_hareket-detay`
FOR EACH ROW TheTrigger : BEGIN
	update `WINDOWS`.`WIN_SIPARISDETAY` a, 
		(select x.idsiparis, sum(x.adet)  as adet from `WINDOWS`.`WIN_SIPARISTABLO` x where x.tip = OLD.`HD-TIP` and x.evrakno = OLD.`HD-EVRAKNO` and x.sira = OLD.`HD-SIRA` group by x.idsiparis) b 
		set a.sevkadet = a.sevkadet - b.adet where a.id = b.idsiparis;
    DELETE from `WINDOWS`.`WIN_SIPARISTABLO` where tip = OLD.`HD-TIP` and evrakno = OLD.`HD-EVRAKNO` and sira = OLD.`HD-SIRA`;
END

//

DELIMITER ;