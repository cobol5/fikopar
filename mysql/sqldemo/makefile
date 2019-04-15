PROGRAM = sqldemo.exe

INCLUDEDIRS = \
    /I "C:\Program Files\MySQL\MySQL Connector C 6.1\include" \
	/I "C:\Program Files\Windows Kits\8.1\Include\um" \
	/I "C:\Program Files\Windows Kits\8.1\Include\shared" \
	/I "C:\Program Files\Microsoft Visual Studio 14.0\VC\include"
	
C = cl.exe

CSOURCES = sqldemo.c

CFLAGS = /nologo /c /MT $(INCLUDEDIRS)

LINK = link.exe

LINKOBJECTS = sqldemo.obj

LINKLIBS = \
	"C:\Program Files\MySQL\MySQL Connector C 6.1\lib\libmysql.lib" \
	"C:\Program Files\Windows Kits\8.1\Lib\winv6.3\um\x86\uuid.lib" \
	"C:\program files\Microsoft Visual Studio 14.0\VC\lib\oldnames.lib" \
	"C:\program files\Microsoft Visual Studio 14.0\VC\lib\libcmt.lib" \
#	"C:\program files\Microsoft Visual Studio 11.0\VC\lib\msvcrt.lib" \
	"C:\Program Files\windows kits\8.1\lib\winv6.3\um\x86\user32.lib" \
	"C:\Program Files\windows kits\8.1\lib\winv6.3\um\x86\kernel32.lib"
	
	
$(PROGRAM): $(LINKOBJECTS)
	$(LINK) /force:multiple /nologo /section:.edata,RD /out:$@ $(LINKOBJECTS) $(LINKLIBS)

sqldemo.obj : sqldemo.c
	$(C) $(CFLAGS) sqldemo.c
	
clean:
	del $(LINKOBJECTS) $(PROGRAM) sqldemo.lib sqldemo.exp
