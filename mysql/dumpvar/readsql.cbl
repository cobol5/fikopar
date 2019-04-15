       IDENTIFICATION DIVISION.
       PROGRAM-ID. 'READSQL'.
       DATA DIVISION.
       WORKING-STORAGE SECTION.
       01 SQLPARAMETERS.
          02 SQLQUERY   PIC X(1024).
       01 HD-KAYIT.
	      02 IZLE		PIC 9 VALUE 0.
          02 KATNO      PIC 9999 VALUE 1.
          02 STNO   PIC X(30) VALUE "037022".
          02 ISK1		PIC 9(12)V9999.
		  02 ISK2		PIC 9(12)V9999.
		  02 MALIYET	PIC 9(12)V9999.
		  02 AZ			PIC Z,ZZZ,ZZZ,ZZZ.ZZZZ.
       77 RVAR  PIC 9.     
       77 DUR   PIC X.
       PROCEDURE DIVISION.
       BASLA.
           MOVE ALL SPACES TO SQLQUERY.
           STRING "select " DELIMITED BY SIZE
				  "iskonto1," DELIMITED BY SIZE 
				  "iskonto2," DELIMITED BY SIZE 
				  "maliyet" DELIMITED BY SIZE
				  " from " DELIMITED BY SIZe
                  "windows.win_alimdetay " DELIMITED BY SIZE
                  "where katno = :1" DELIMITED BY SIZE
                  " and stno = ':2'" DELIMITED BY SIZE
                  INTO SQLQUERY.
       OKU.
	       DISPLAY SQLQUERY LINE 1 POSITION 1 
		     CONTROL 'BCOLOR=WHITE, FCOLOR=BLACK'.
           
		   DISPLAY "STNO    [" LINE 3 POSITION 1 STNO "]".
		   MOVE ISK1 TO AZ.
	       DISPLAY "ISK1    [" LINE 5 POSITION 1 AZ "]".
		   MOVE ISK2 TO AZ.
	       DISPLAY "ISK2    [" LINE 6 POSITION 1 AZ "]".
		   MOVE MALIYET TO AZ.
	       DISPLAY "MALIYET [" LINE 7 POSITION 1 AZ "]".
		     		   
           CALL "exec_scaler" USING IZLE "diesel.dyndns.biz" "root"
                "1413" SQLQUERY KATNO STNO ISK1 ISK2 MALIYET.		   
           CANCEL "exec_scaler".
           
		   
		   DISPLAY SQLQUERY LINE 9 POSITION 1 
		     CONTROL 'BCOLOR=WHITE, FCOLOR=BLACK'.
           
		   DISPLAY "STNO    [" LINE 11 POSITION 1 STNO "]".
		   MOVE ISK1 TO AZ.
	       DISPLAY "ISK1    [" LINE 12 POSITION 1 AZ "]".
		   MOVE ISK2 TO AZ.
	       DISPLAY "ISK2    [" LINE 13 POSITION 1 AZ "]".
		   MOVE MALIYET TO AZ.
	       DISPLAY "MALIYET [" LINE 14 POSITION 1 AZ "]".
		   
      *    CALL "dump_variable" USING IZLE "diesel.dyndns.biz" "root"
      *     "1413" SQLQUERY KATNO STNO ISK1 ISK2 MALIYET.		   
      *    CANCEL "dump_variable".
           
		   
           ACCEPT DUR NO BEEP.
       SON.
           EXIT PROGRAM.
           STOP RUN.
       