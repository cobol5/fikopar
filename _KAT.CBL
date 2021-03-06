       IDENTIFICATION DIVISION.
       PROGRAM-ID. KAT.
       DATA DIVISION.
       WORKING-STORAGE SECTION.
       77 KEY-CODE      PIC X.
       77 F             PIC 9999.
       77 X             PIC 9999 VALUE 5.
       77 Y             PIC 9999 VALUE 6.
	   77 INSERT-MODE   PIC 9 BINARY VALUE 0.
	   77 CURPOS		PIC 999 VALUE 0.
	   01 SCREEN-VALUES.
			02 STRING-LENGTH PIC 999.
			02 STRING-VALUE         PIC X(255).
       01 TERM-EX               PIC 9(3).
       01 EXIT-CODE     PIC 9(8) BINARY.
       PROCEDURE DIVISION.
       BASLA.
           ACCEPT KEY-CODE LINE Y POSITION X.
           ACCEPT F FROM ESCAPE KEY.
		   IF F = ZEROS 
					MOVE KEY-CODE TO STRING-VALUE(CURPOS).
           IF F = 11 AND X > 1 SUBTRACT 1 FROM X.
           IF F = 12 AND X < 120 ADD 1 TO X.
           IF F = 27 GO STOP-PROGRAM.
           IF F = 14 AND INSERT-MODE = 0
             CALL 'C$GUICFG' USING 'Cursor Overtype=FullBox' EXIT-CODE
             MOVE 1 TO INSERT-MODE GO BASLA.
           IF F = 14 AND INSERT-MODE = 1
             CALL 'C$GUICFG' USING 'Cursor Overtype=HorzLine' EXIT-CODE
             MOVE 0 TO INSERT-MODE GO BASLA.
           DISPLAY X LINE 10 POSITION 10 ',' Y ',' F.
		   ADD 1 TO X.
           GO BASLA.
	   STOP-PROGRAM.
	       PERFORM VARYING X FROM 1 BY 1 UNTIL X > LENGTH
				DISPLAY STRING-VALUE(X) LINE 25 POSITION X
		   END-PERFORM.
		   ACCEPT KEY-CODE LINE 26 POSITION 1.
		   EXIT PROGRAM.
		   STOP RUN.