#include <windows.h>
#define RMLittleEndian 1
#include "rmc85cal.h"
#include <stdio.h>

int __declspec(dllexport)  __cdecl address_test(char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	
	AllocConsole();
	freopen("CONIN$", "r",stdin);
	freopen("CONOUT$", "w",stdout);
	freopen("CONOUT$", "w",stderr);

	printf("This is function name is address_test");

	return (RM_FND);
}
