       IDENTIFICATION DIVISION.
       PROGRAM-ID. 'READSQL'.
       DATA DIVISION.
       WORKING-STORAGE SECTION.
       01 SQLPARAMETERS.
          02 SQLQUERY   PIC X(1024).
       77 STNO      PIC X(15) VALUE "031708".
       77 RVAR  PIC 9.     
       77 DUR   PIC X.
       PROCEDURE DIVISION.
       BASLA.
           MOVE ALL SPACES TO SQLQUERY.
           STRING "select * from " DELIMITED BY SIZE
                  "windows.win_alimdetay " DELIMITED BY SIZE
                  "where stno = '" DELIMITED BY SIZE
                  STNO DELIMITED BY SPACE
                  "'" DELIMITED BY SIZE
                  INTO SQLQUERY.
       OKU.
           CALL "exec_query" USING "diesel.dyndns.biz" "root" "1413"
                SQLQUERY.
           CANCEL "exec_query".
           
           DISPLAY SQLQUERY.
           ACCEPT DUR NO BEEP.
       SON.
           EXIT PROGRAM.
           STOP RUN.
       