#include <windows.h>
#include <sql.h>
#include <sqlext.h>
#include <stdio.h>
#include <conio.h>
#include <tchar.h>
#include <stdlib.h>
#include <sal.h>

#define TRYODBC(h, ht, x)   {   RETCODE rc = x;\
                                if (rc != SQL_SUCCESS) \
                                { \
                                    HandleDiagnosticRecord (h, ht, rc); \
                                } \
                                if (rc == SQL_ERROR) \
                                { \
                                    fwprintf(stderr, L"Error in " L#x L"\n"); \
                                    goto Exit;  \
                                }  \
                            }

#define SQL_QUERY_SIZE      1000


void HandleDiagnosticRecord(SQLHANDLE      hHandle,
	SQLSMALLINT    hType,
	RETCODE        RetCode);


int __cdecl wmain(int argc, _In_reads_(argc) WCHAR **argv)
{
	SQLHENV     hEnv = NULL;
	SQLHDBC     hDbc = NULL;
	SQLHSTMT    hStmt = NULL;
	WCHAR * pwszConnStr = L"DRIVER = {Microsoft Excel Driver (*.xls)};DSN=Excel Files;DBQ=C:\\Users\\Fikret\\documents\\result.xls;DefaultDir=C:\\Users\\Fikret\\documents;DriverId=1046;MaxBufferSize=2048;PageTimeout=5;";
	//char ConnOut[200];
	//WCHAR       wszInput[SQL_QUERY_SIZE];

	// Allocate an environment
	if (SQLAllocHandle(SQL_HANDLE_ENV, SQL_NULL_HANDLE, &hEnv) == SQL_ERROR)
	{
		fwprintf(stderr, L"Unable to allocate an environment handle\n");
		exit(-1);
	}

	// Register this as an application that expects 3.x behavior,
	// you must register something if you use AllocHandle
	
	TRYODBC(hEnv,
		SQL_HANDLE_ENV,
		SQLSetEnvAttr(hEnv,
			SQL_ATTR_ODBC_VERSION,
			(SQLPOINTER)SQL_OV_ODBC3,
			0));

	
	// Allocate a connection
	TRYODBC(hEnv,
		SQL_HANDLE_ENV,
		SQLAllocHandle(SQL_HANDLE_DBC, hEnv, &hDbc));

	// Connect to the driver.  Use the connection string if supplied
	// on the input, otherwise let the driver manager prompt for input.
	
	fwprintf(stderr, L"Connection String : %s\n", (SQLWCHAR *)pwszConnStr);
	fwprintf(stderr, L"Connection Size : %d\n", wcslen(pwszConnStr));
	//HWND desktopHandle = GetDesktopWindow();
	
	TRYODBC(hDbc,
		SQL_HANDLE_DBC,
		SQLDriverConnect(hDbc,
			NULL,
			(SQLWCHAR *)pwszConnStr,
			wcslen(pwszConnStr),
			NULL,
			0,
			NULL,
			SQL_DRIVER_COMPLETE));
	
	//fwprintf(stderr, L"Connection String : %s\n", ConnOut);
	//fwprintf(stderr, L"Connection Size : %d\n", sizeof(ConnOut));
	fwprintf(stderr, L"Connected!\n");
	getchar();
Exit:
	// Free ODBC handles and exit

	if (hStmt)
	{
		SQLFreeHandle(SQL_HANDLE_STMT, hStmt);
	}

	if (hDbc)
	{
		SQLDisconnect(hDbc);
		SQLFreeHandle(SQL_HANDLE_DBC, hDbc);
	}

	if (hEnv)
	{
		SQLFreeHandle(SQL_HANDLE_ENV, hEnv);
	}

	wprintf(L"\nDisconnected.");

	return 0;
}



void HandleDiagnosticRecord(SQLHANDLE      hHandle,
	SQLSMALLINT    hType,
	RETCODE        RetCode)
{
	SQLSMALLINT iRec = 0;
	SQLINTEGER  iError;
	WCHAR       wszMessage[1000];
	WCHAR       wszState[SQL_SQLSTATE_SIZE + 1];


	if (RetCode == SQL_INVALID_HANDLE)
	{
		fwprintf(stderr, L"Invalid handle!\n");
		return;
	}

	while (SQLGetDiagRec(hType,
		hHandle,
		++iRec,
		wszState,
		&iError,
		wszMessage,
		(SQLSMALLINT)(sizeof(wszMessage) / sizeof(WCHAR)),
		(SQLSMALLINT *)NULL) == SQL_SUCCESS)
	{
		// Hide data truncated..
		if (wcsncmp(wszState, L"01004", 5))
		{
			fwprintf(stderr, L"[%5.5s] %s (%d)\n", wszState, wszMessage, iError);
		}
	}

}
