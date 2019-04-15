#include <windows.h>
#include "rmc85cal.h"
#include <mysql.h>
#include <stdio.h>

int __declspec(dllexport)  __cdecl dump_variable(char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	WORD i = 0;
	double var = 0;
	AllocConsole();
	freopen("CONIN$", "r",stdin);
	freopen("CONOUT$", "w",stdout);
	freopen("CONOUT$", "w",stderr);

	printf("Name	: [%s]\n", Name);
	printf("ArgCount: [%d]\n", ArgCount);
	printf("State	: [%d]\n", State);
	
	for (i = 0; i < ArgCount; i++) {
		printf("----------------------ArgEntry[%d]-----------------------\n", i);
		//printf("a_address	: [%s]\n", ArgEntry[i].a_address);
		
		printf("a_type      : [%d]\n", ArgEntry[i].a_type);
		printf("a_length    : [%d]\n", ArgEntry[i].a_length);
		printf("a_scale     : [%d]\n", ArgEntry[i].a_scale);
		printf(" ---- PARSE VALUES ----------\n");
		char * s = geta(&ArgEntry[i]);	
		printf("str         : [%s]\n", s);
		
		float ft = getf(&ArgEntry[i]);
		printf("float       : [%f]\n", ft);
		puta(s, &ArgEntry[i]);
		
		free(s);
		
		char *k = geta(&ArgEntry[i]);
		putf(ft,&ArgEntry[i]);
		printf(" ---- AFTER SET ----------\n");
		printf("str         : [%s]\n", k);
		printf("---------------------------------------------------------\n");
		
		free(k);
	}
	
	
	
	return (RM_FND);
}
