       01 SCR-ONAY.
           02 LINE 16 COLUMN 33 BACKGROUND BLACK FOREGROUND WHITE
            '�����������������������������������������������������ͻ'
             HIGHLIGHT.
           02 LINE 17 COLUMN 33 BACKGROUND BLACK FOREGROUND WHITE
            '�                                                     �'
             HIGHLIGHT.
           02 LINE 18 COLUMN 33 BACKGROUND BLACK FOREGROUND WHITE
            '� ' HIGHLIGHT. 02 PIC X(43) USING ONAY-MESSAGE HIGHLIGHT.
           02 ' [ ] E/H �' HIGHLIGHT.
           02 LINE 19 COLUMN 33 BACKGROUND BLACK FOREGROUND WHITE
            '�                                                     �'
            HIGHLIGHT.
           02 LINE 20 COLUMN 33 BACKGROUND BLACK FOREGROUND WHITE
            '�����������������������������������������������������ͼ'
            HIGHLIGHT.
           02 SCR-ONAY-CVP.
               03 LINE 18 COLUMN 80 BACKGROUND BLACK FOREGROUND WHITE
                PIC X USING E-ONAY HIGHLIGHT.
       01 SCR-MESSAGE.
           02 LINE 16 COLUMN 33 BACKGROUND RED FOREGROUND WHITE
            '�����������������������������������������������������ͻ'
             HIGHLIGHT.
           02 LINE 17 COLUMN 33 BACKGROUND RED FOREGROUND WHITE
            '�                                                     �'
             HIGHLIGHT.
           02 LINE 18 COLUMN 33 BACKGROUND RED FOREGROUND WHITE
            '� ' HIGHLIGHT. 02 PIC X(51) USING ONAY-MESSAGE HIGHLIGHT.
           02 ' �' HIGHLIGHT.
           02 LINE 19 COLUMN 33 BACKGROUND RED FOREGROUND WHITE
            '�                                                     �'
            HIGHLIGHT.
           02 LINE 20 COLUMN 33 BACKGROUND RED FOREGROUND WHITE
            '�����������������������������������������������������ͼ'
            HIGHLIGHT.
                