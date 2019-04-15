       IDENTIFICATION DIVISION.
       PROGRAM-ID. MYSQLCBL.
       DATA DIVISION.
       WORKING-STORAGE SECTION.
       01 HS-KAYIT.
           02 HS-KEY.
               03 HS-TIP                PIC 99.
               03 HS-EVRAKNO            PIC X(15).
           02 HS-TARIH.
               03 HS-DYIL               PIC 9999.
               03 HS-DAY                PIC 99.
               03 HS-DGUN               PIC 99.
           02 HS-SAAT.
               03 HS-SA                 PIC 99.
               03 HS-DD                 PIC 99.
               03 HS-SS                 PIC 99.                
           02 HS-FTARIH.
               03 HS-FYIL               PIC 9999.
               03 HS-FAY                PIC 99.
               03 HS-FGUN               PIC 99.
           02 HS-HESAPNO                PIC X(15).
           02 HS-HESAPADI               PIC X(50).
           02 HS-YETKILI                PIC X(40).
           02 HS-ADRES1                 PIC X(40).
           02 HS-ADRES2                 PIC X(40).
           02 HS-ADRES3                 PIC X(40).
           02 HS-MAHALLE                PIC X(20).
           02 HS-ILCE                   PIC X(20).
           02 HS-IL                     PIC X(20).
           02 HS-ULKE                   PIC X(20).
           02 HS-VDA                    PIC X(30).
           02 HS-VNO                    PIC X(10).
           02 HS-TCNO                   PIC X(11).
           02 HS-TSNO                   PIC X(11).
           02 HS-MERSIS                 PIC X(20).
           02 HS-GUN                    PIC 99999.
           02 HS-PLKOD					PIC 9(4).
           02 HS-DEPONO                 PIC 9999.
           02 HS-DUZEN                  PIC 9(4).
           02 HS-BAG OCCURS 2 TIMES.
               03 HS-BAGTIPI                PIC 99.
               03 HS-BEVRAKNO               PIC X(15).
           02 HS-SONUC                  PIC 99.
	   77  DUR				PIC X.
       PROCEDURE DIVISION.
       BASLA.
      *     CALL "executesql" USING "INSERT" "STOK" ST-KAYIT 
      *      RETURN-BINARY.
	       INITIALIZE HS-KAYIT.
		   MOVE 1 TO HS-TIP.
		   MOVE "Z-2015000011" TO HS-EVRAKNO.
		   MOVE 20160307 TO HS-TARIH.
		   MOVE 125301 TO HS-SAAT.
		   MOVE 20150206 TO HS-FTARIH.
		   MOVE '000010' TO HS-HESAPNO.
		   MOVE 'DTR' TO HS-HESAPADI.
		   MOVE 'AP' TO HS-YETKILI.
		   MOVE '' TO HS-ADRES1.
		   MOVE SPACES TO HS-ADRES2.
		   MOVE '' TO HS-ADRES3.
		   MOVE '' TO HS-MAHALLE.
		   MOVE '' TO HS-ILCE.
		   MOVE '' TO HS-IL.
		   MOVE '' TO HS-ULKE.
		   MOVE '' TO HS-VDA.
		   MOVE '' TO HS-VNO.
		   MOVE '' TO HS-TCNO.
		   MOVE '' TO HS-TSNO.
		   MOVE '' TO HS-MERSIS.
		   MOVE 90 TO HS-GUN.
		   MOVE 1 TO HS-PLKOD.
		   MOVE 2 TO HS-DEPONO.
		   MOVE 3 TO HS-DUZEN.
		   MOVE 5 TO HS-BAGTIPI(1).
		   MOVE 'MK0005' TO HS-BEVRAKNO(1).
		   MOVE 6 TO HS-BAGTIPI(2).
		   MOVE 'MK0006' TO HS-BEVRAKNO(2).
		   MOVE 0 TO HS-SONUC.
	   TEKRAR.
	       CALL "executesql" USING "****remotedns****" "root" "root" 
		     "COBOL" "HAREKET-SICIL" "INSERT" HS-KAYIT.
		   CANCEL "executesql".
           ACCEPT DUR NO BEEP.
           IF DUR NOT = "Q" THEN GO TEKRAR.
           EXIT PROGRAM.
		   