PROGRAM = mysql.dll

INCLUDEDIRS = \
    /I "C:\Program Files\MySQL\MySQL Connector C 6.1\include" \
	/I "C:\Program Files\Windows Kits\8.1\Include\um" \
	/I "C:\Program Files\Windows Kits\8.1\Include\shared" \
	/I "C:\Program Files\Microsoft Visual Studio 14.0\VC\include" \
	/I "C:\Program Files\iconv-1.9.2.win32\include"
	
C = cl.exe

CSOURCES = rmc85cal.c dumpvar.c sqlhelper.c mysql.c

CFLAGS = /nologo /c /MT $(INCLUDEDIRS)

LINK = link.exe

LINKOBJECTS = rmc85cal.obj dumpvar.obj sqlhelper.obj mysql.obj

LINKLIBS = \
	"C:\Program Files\MySQL\MySQL Connector C 6.1\lib\libmysql.lib" \
	"C:\Program Files\Windows Kits\8.1\Lib\winv6.3\um\x86\uuid.lib" \
	"C:\program files\Microsoft Visual Studio 14.0\VC\lib\oldnames.lib" \
	"C:\program files\Microsoft Visual Studio 14.0\VC\lib\libcmt.lib" \
#	"C:\program files\Microsoft Visual Studio 11.0\VC\lib\msvcrt.lib" \
	"C:\Program Files\windows kits\8.1\lib\winv6.3\um\x86\user32.lib" \
	"C:\Program Files\windows kits\8.1\lib\winv6.3\um\x86\kernel32.lib" \
	"C:\Program Files\iconv-1.9.2.win32\lib\iconv.lib"
	
	
$(PROGRAM): $(LINKOBJECTS)
	$(LINK) /nologo /dll /section:.edata,RD /subsystem:windows /out:$@ $(LINKOBJECTS) $(LINKLIBS)

mysql.obj : mysql.c
	$(C) $(CFLAGS) mysql.c

rmc85cal.obj : rmc85cal.c
	$(C) $(CFLAGS) rmc85cal.c
	
dumpvar.obj : dumpvar.c
	$(C) $(CFLAGS) dumpvar.c

sqlhelper.obj : sqlhelper.c
	$(C) $(CFLAGS) sqlhelper.c
	
clean:
	del $(LINKOBJECTS) $(PROGRAM) mysql.lib mysql.exp
