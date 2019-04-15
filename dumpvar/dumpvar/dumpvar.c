#include <windows.h>
#define RMLittleEndian 1
#include "rmc85cal.h"
#include <stdio.h>

int __declspec(dllexport)  __cdecl dump_variable(char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	WORD i = 0;

	AllocConsole();
	freopen("CONIN$", "r",stdin);
	freopen("CONOUT$", "w",stdout);
	freopen("CONOUT$", "w",stderr);

	printf("Name	: [%s]\n", Name);
	printf("ArgCount: [%d]\n", ArgCount);
	printf("State	: [%d]\n", State);
	
	for (i = 0; i < ArgCount; i++) {
		printf("----------------------ArgEntry[%d]-----------------------\n", i);
		printf("a_address	: [%s]\n", ArgEntry[i].a_address);
		printf("a_digits	: [%d]\n", ArgEntry[i].a_digits);
		printf("a_length	: [%d]\n", ArgEntry[i].a_length);
		printf("a_picture	: [%s]\n", ArgEntry[i].a_picture);
		printf("a_scale		: [%d]\n", ArgEntry[i].a_scale);
		printf("a_type		: [%d]\n", ArgEntry[i].a_type);
		printf("---------------------------------------------------------\n");
	}
	return (RM_FND);
}
