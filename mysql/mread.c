#include <windows.h>
#define RMLittleEndian 1
#include "rmc85cal.h"
#include <stdio.h>
#include <mysql.h>
#include "codec.c"


struct OPENTABLE {
	MYSQL *con;
	MYSQL_RES * rs;
	char * name;
	char * tablename;
	char * data;
	struct OPENTABLES *next;
	struct TABLE *table;
}

struct OPENTABLE *openhead = NULL;
struct OPENTABLE *openlast = NULL;

struct OPNTABLE * getOpenTable(char *tablename,char * name, BOOL create) {
	struct OPENTABLE *ptr = openhead;
	
	while(ptr!=NULL) {
		if(strcmp(ptr->tablename,tablename) == 0 && strcmp(ptr->name,name) == 0)
			return ptr;
		ptr = ptr->next;
	}
	if(create == FALSE)
		return NULL;
	ptr = (struct OPENTABLE*)malloc(sizeof(struct OPENTABLE));
	ptr->tablename = tablename;
	ptr->name = name;
	ptr->table = (struct TABLE*)malloc(sizeof(struct TABLE));
	ptr->table->head = NULL;
	ptr->table->last = NULL;
	ptr->table->next = NULL;
	ptr->next = NULL;
	
	if(openlast == NULL) 
		openhead = openlast = ptr;
	else {
		openlast->next = ptr;
		openlast = ptr;
	}
	return ptr;
}
int __declspec(dllexport)  __cdecl open(char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	
	MYSQL *con;
	char server[20];
	char user[20];
	char password[20];
	char database[20];
	char tablename[30];
	char optype[20];
	char data[2048];
    
	char sql[2048];
	char msgbuffer[2048];
    
	strncpy(server,ArgEntry[0].a_address,ArgEntry[0].a_length);
	server[ArgEntry[0].a_length] = '\0';
	strncpy(user,ArgEntry[1].a_address,ArgEntry[1].a_length);
	user[ArgEntry[1].a_length] = '\0';
	strncpy(password,ArgEntry[2].a_address,ArgEntry[2].a_length);
	password[ArgEntry[2].a_length] = '\0';
	strncpy(database,ArgEntry[3].a_address,ArgEntry[3].a_length);
	database[ArgEntry[3].a_length] = '\0';
	strcpy(tablename, "COB_");
	strncpy(tablename+4,ArgEntry[4].a_address,ArgEntry[4].a_length);
	tablename[ArgEntry[4].a_length+4] = '\0';
	
	MYSQL *con = mysql_init(con);
		
	mysql_options(con, MYSQL_SET_CHARSET_NAME, charset);
	mysql_options(con, MYSQL_OPT_RECONNECT, &reconnect);
	mysql_options(con, MYSQL_OPT_CONNECT_TIMEOUT, "60");
	mysql_options(con, MYSQL_INIT_COMMAND, autocommit);
		
	if (!mysql_real_connect(con, server, user, password, database, 0, NULL, 0)) {
		sprintf(msgbuffer, "%s", mysql_error(conn));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		return (RM_STOP);
	}
        
	sprintf(sql, "SELECT A.COLUMN_NAME, UPPER(A.DATA_TYPE),A.CHARACTER_MAXIMUM_LENGTH, A.NUMERIC_PRECISION, A.NUMERIC_SCALE, \
	        CASE B.INDEX_NAME WHEN 'COB_KEY' THEN 1 ELSE NULL END AS COB_KEY FROM  \
		    INFORMATION_SCHEMA.COLUMNS A \
			LEFT JOIN INFORMATION_SCHEMA.STATISTICS B ON A.TABLE_SCHEMA = B.TABLE_SCHEMA AND A.TABLE_NAME = B.TABLE_NAME AND A.COLUMN_NAME = B.COLUMN_NAME \
			where A.TABLE_SCHEMA = 'COBOL' AND A.TABLE_NAME LIKE '%s' AND UPPER(A.COLUMN_NAME) <> 'ID' \
		   ORDER BY A.TABLE_NAME, A.ORDINAL_POSITION", tablename);
	
	if (mysql_query(con, sql)) {
        sprintf(msgbuffer, "%s", mysql_error(conn));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		return FALSE;
    }
	res = mysql_use_result(con);

	struct OPENTABLE *ptr = getTable(tablename, name, TRUE);
	struct COLUMN *col;
	
	while(row = mysql_fetch_row(res)) {
		ptr = getTable(strdup(row[0]), TRUE);
		col = (struct COLUMN*)malloc(sizeof(struct COLUMN));
		col->columnname = strdup(row[1]);
		
		if(strcmp(row[2],"VARCHAR") == 0) {
			col->type = _VARCHAR;
			col->length = atoi(row[3]);
			col->scale = 0;
		} else {
			if(strcmp(row[2], "DECIMAL") == 0) 
				col->type = _DECIMAL;
			else
				col->type = _INTEGER;
			col->length = atoi(row[4]);
			col->scale = atoi(row[5]);
		}
		
		col->isKey = row[6] ? TRUE : FALSE;
		col->next = NULL;
		
		if(ptr->last == NULL) {
			ptr->head = ptr->last = col;
		} else {
			ptr->last->next = col;
			ptr->last = col;
		}	
	}
	
	mysql_free_result(res);
    
	return TRUE;
	return (RM_FND);
}
