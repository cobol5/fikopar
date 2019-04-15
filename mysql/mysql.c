#include <windows.h>
#define RMLittleEndian 1
#include "rmc85cal.h"
#include <stdio.h>
#include <mysql.h>
#include "codec.c"

#define READNEXT 1
#define READPREVIOUS -1
#define READEQUAL 0

struct TABLE {
	char * tablename;
	struct COLUMN * head;
	struct COLUMN * last;
	struct TABLE * next;
};


struct TABLE *head = NULL;
struct TABLE *last = NULL;

MYSQL *conn = NULL;
my_bool reconnect = 1;
char *charset = "utf8";
char *autocommit = "SET autocommit=0";

int __declspec(dllexport)  __cdecl dump_table(char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	struct TABLE *ptr = head;
	struct COLUMN *col;
	
	AllocConsole();	
	
	while(ptr!=NULL) {
		printf("Table Name : %s\n", ptr->tablename);
		printf("--------------------------------\n");
		col = ptr->head;
		while(col!=NULL) {
			printf("%s, %d, %d, %d, %d\n", col->columnname, col->type, col->length, col->scale, col->isKey);
			col = col->next;
		}		
		printf("--------------------------------\n");
		ptr=ptr->next;
	}
	return (RM_FND);
}

struct TABLE * getTable(char *tablename, BOOL create) {
	struct TABLE *ptr = head;
	
	while(ptr!=NULL) {
		if(strcmp(ptr->tablename,tablename) == 0)
			return ptr;
		ptr = ptr->next;
	}
	if(create == FALSE)
		return NULL;
	ptr = (struct TABLE*)malloc(sizeof(struct TABLE));
	ptr->tablename = tablename;
	ptr->head = NULL;
	ptr->last = NULL;
	ptr->next = NULL;
	
	if(last == NULL) 
		head = last = ptr;
	else {
		last->next = ptr;
		last = ptr;
	}
	return ptr;
}
BOOL fillTables () {
	MYSQL_RES *res;
	MYSQL_ROW row;
	char msgbuffer[2048];
	
	char *sql = "SELECT UPPER(A.TABLE_NAME), A.COLUMN_NAME, UPPER(A.DATA_TYPE),A.CHARACTER_MAXIMUM_LENGTH, A.NUMERIC_PRECISION, A.NUMERIC_SCALE, \
	             CASE B.INDEX_NAME WHEN 'COB_KEY' THEN 1 ELSE NULL END AS COB_KEY FROM  \
				 INFORMATION_SCHEMA.COLUMNS A \
				 LEFT JOIN INFORMATION_SCHEMA.STATISTICS B ON A.TABLE_SCHEMA = B.TABLE_SCHEMA AND A.TABLE_NAME = B.TABLE_NAME AND A.COLUMN_NAME = B.COLUMN_NAME \
				 where A.TABLE_SCHEMA = 'COBOL' AND A.TABLE_NAME LIKE 'COB_%' AND UPPER(A.COLUMN_NAME) <> 'ID' \
				 ORDER BY A.TABLE_NAME, A.ORDINAL_POSITION";
	
	struct TABLE *ptr;
	struct COLUMN *col;
	
	if (mysql_query(conn, sql)) {
        sprintf(msgbuffer, "%s", mysql_error(conn));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		return FALSE;
    }
	res = mysql_use_result(conn);

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
}
void createInsertStatement(struct TABLE * ptr, char * data, char sql[], char sql_log[]) {
	char fields[1000];
	char values[1000];
	char tmp[1000];
	struct COLUMN * col = ptr->head;
	int i = 0;
	
	fields[0] = '\0';
	values[0] = '\0';
	
	while(col!=NULL) {
		strncpy(tmp, data+i, col->length);
		cob2sql(tmp, col);
		sprintf(fields,"%s%s`%s`",fields, i==0 ? "" : ",", col->columnname);
		sprintf(values,"%s%s%s",values, i==0 ? "" : ",", tmp);
		i += col->length;
		col = col->next;
	}
	sprintf(sql,"INSERT INTO `%s`(%s) VALUES(%s)", ptr->tablename,fields,values);
	sprintf(sql_log, "INSERT INTO `LOG_COBOL`(`TABLENAME`,`KEYID`,`OPERATION`,`OPTIME`) values ('%s', LAST_INSERT_ID(), 'INSERT', DATE_FORMAT(now(), '%%Y%%m%%d%%H%%i%%s'))", ptr->tablename);
	
}
void createDeleteStatement(struct TABLE * ptr, char * data, char sql[], char sql_log[]) {
	struct COLUMN * col = ptr->head;
	char keys[1000];
	char tmp[1000];
	int i = 0;
	
	keys[0] = '\0';
	while(col!=NULL) {
		if(col->isKey == TRUE) {
			strncpy(tmp, data+i, col->length);
			cob2sql(tmp, col);		
			sprintf(keys,"%s%s`%s` = %s",keys, i==0 ? " " : " and ", col->columnname, tmp);
		}
		i += col->length;
		col = col->next;
	}
	sprintf(sql, "DELETE FROM `%s` WHERE %s", ptr->tablename,keys);
	sprintf(sql_log, "INSERT INTO `LOG_COBOL`(`TABLENAME`,`KEYID`,`OPERATION`,`OPTIME`) values ('%s', (select id from `%s` where %s), \
							'DELETE', DATE_FORMAT(now(), '%%Y%%m%%d%%H%%i%%s'))", ptr->tablename, ptr->tablename, keys);
}
void createUpdateStatement(struct TABLE * ptr, char * data, char sql[] , char sql_log[]) {
	char values[1000];
	char keys[1000];
	char tmp[1000];
	struct COLUMN * col = ptr->head;
	int i = 0;
	
	values[0] = '\0';
	keys[0] = '\0';
	
	while(col!=NULL) {
		strncpy(tmp, data+i, col->length);
		cob2sql(tmp, col);
		sprintf(values,"%s%s`%s`=%s",values, i==0 ? "" : ", ", col->columnname, tmp);
		if(col->isKey == TRUE)
			sprintf(keys,"%s%s`%s`=%s",keys, i==0 ? "" : " and ", col->columnname, tmp);
		i += col->length;
		col = col->next;
	}
	sprintf(sql,"UPDATE `%s` SET %s where %s", ptr->tablename,values,keys);
	sprintf(sql_log, "INSERT INTO `LOG_COBOL`(`TABLENAME`,`KEYID`,`OPERATION`,`OPTIME`) values ('%s', (select id from `%s` where %s), \
							'UPDATE', DATE_FORMAT(now(), '%%Y%%m%%d%%H%%i%%s'))", ptr->tablename, ptr->tablename, keys);
}

BOOLEAN read(struct TABLE * ptr, char * data, int dir) {
	MYSQL_RES *res;
	MYSQL_ROW row;
	char msgbuffer[2048];
	char sql[2048];
	char keys[1000];
	char tmp[1000];
	int i = 0, p = 0;
	struct COLUMN * col = ptr->head;
	
	AllocConsole();	
	freopen("CONOUT$", "w", stdout);

	sql[0] = '\0';
	keys[0] = '\0';
	while(col!=NULL) {
		if(col->isKey == TRUE) {
			strncpy(tmp, data+i, col->length);
			cob2sql(tmp, col);
			sprintf(keys,"%s%s`%s`%s%s",keys, keys[0] == '\0' ? "" : " and ", col->columnname, dir == READNEXT ? ">" : (dir == READPREVIOUS ? "<" : "=") , tmp);
		}
		sprintf(sql,"%s%s`%s`", sql, col == ptr->head ? "SELECT " : ",", col->columnname);
		i += col->length;
		col = col->next;
	}
	sprintf(sql,"%s FROM `%s` WHERE %s LIMIT 1", sql, ptr->tablename, keys);
	
	printf("%s\n",sql);
	
	if (mysql_query(conn, sql)) {
        sprintf(msgbuffer, "%s", mysql_error(conn));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		return FALSE;
    }
	res = mysql_use_result(conn);

	if(row = mysql_fetch_row(res)) {
		i = 0;
		p = 0;
		col = ptr->head;
		while(col!=NULL) {
			sql2cob(data, row[i++], p, col);
			p += col->length;
			col = col->next;
		}
		mysql_free_result(res);
		return TRUE;
	}
	mysql_free_result(res);
	return FALSE;
}

int __declspec(dllexport)  __cdecl exitprogram (char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	mysql_close(conn);
	return (RM_FND);	
}
int __declspec(dllexport)  __cdecl commit (char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	mysql_commit(conn);
	return (RM_FND);	
}
int __declspec(dllexport)  __cdecl rollback (char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	mysql_rollback(conn);
	return (RM_FND);	
}

int __declspec(dllexport)  __cdecl executesql (char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	/*
	 *  0 -> mysql_server_host
	 *  1 -> username
	 *  2 -> password
	 *  3 -> database_name
	 *  4 -> tablename
	 *  5 -> optype 
	 *  6 -> data
	 */
	//char *server = (char *)malloc( ArgEntry[0].a_length);
	char server[20];
	//char *user = (char *)malloc( ArgEntry[1].a_length);
	char user[20];
	//char *password = (char *)malloc( ArgEntry[2].a_length);
	char password[20];
	//char *database = (char *)malloc( ArgEntry[3].a_length);
	char database[20];
	//char *tablename =  (char *)malloc( ArgEntry[4].a_length + 4);
	char tablename[30];
	// char *optype =  (char *)malloc( ArgEntry[5].a_length);
	char optype[20];
	//char *data = (char *)malloc( ArgEntry[6].a_length);
	char data[2048];
    
	char sql[2048];
	char sql_log[2048];
	struct TABLE * ptr;
	char msgbuffer[2048];
    
	if(server ==NULL || user == NULL || password == NULL || database == NULL || tablename == NULL || optype == NULL || data == NULL)
		return RM_STOP;
	
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
	
	strncpy(optype,ArgEntry[5].a_address,ArgEntry[5].a_length);
	optype[ArgEntry[5].a_length] = '\0';
	
	if(strcmp(optype,"DELETEALL") != 0 && strcmp(optype,"TRUNCATE") != 0) {
		strncpy(data,ArgEntry[6].a_address,ArgEntry[6].a_length);
		data[ArgEntry[6].a_length] = '\0';
	}
	
	/*
	AllocConsole();
	printf("[%s]:[%d]\n",server,ArgEntry[0].a_length);
	printf("[%s]:[%d]\n",user,ArgEntry[1].a_length);
	printf("[%s]:[%d]\n",password,ArgEntry[2].a_length);
	printf("[%s]:[%d]\n",database,ArgEntry[3].a_length);
	printf("[%s]:[%d]\n",tablename,ArgEntry[4].a_length+4);
	printf("[%s]:[%d]\n",optype,ArgEntry[5].a_length);
	printf("[%s]:[%d]\n",data,ArgEntry[6].a_length);
	*/
	
	if(head == NULL) {
		conn = mysql_init(conn);
		
		mysql_options(conn, MYSQL_SET_CHARSET_NAME, charset);
		mysql_options(conn, MYSQL_OPT_RECONNECT, &reconnect);
		mysql_options(conn, MYSQL_OPT_CONNECT_TIMEOUT, "60");
		mysql_options(conn, MYSQL_INIT_COMMAND, autocommit);
		
		if (!mysql_real_connect(conn, server, user, password, database, 0, NULL, 0)) {
			sprintf(msgbuffer, "%s", mysql_error(conn));
			MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
			return (RM_STOP);
		}
        
		if(fillTables() == FALSE) {
			mysql_close(conn);
			return (RM_STOP);
		}
	}
	
	ptr = getTable(tablename, FALSE);
	
	if(ptr == NULL) {
		sprintf(msgbuffer, "[%s] tablo yaps bulunamad.", strcat("COB_",tablename));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		mysql_close(conn);
		return (RM_STOP);
	}
	
	if(strcmp(optype,"INSERT") == 0)
		createInsertStatement(ptr, data, sql, sql_log);
	else if(strcmp(optype,"DELETE") == 0)
		createDeleteStatement(ptr, data, sql, sql_log);
	else if(strcmp(optype,"UPDATE") == 0)
		createUpdateStatement(ptr, data, sql, sql_log);
	else if(strcmp(optype,"TRUNCATE") == 0 || strcmp(optype,"DELETEALL") == 0) {
		sprintf(sql,"TRUNCATE TABLE `%s`", ptr->tablename);
		sprintf(sql_log, "INSERT INTO `LOG_COBOL`(`TABLENAME`,`KEYID`,`OPERATION`,`OPTIME`) values ('%s', 0, 'TRUNCATE', DATE_FORMAT(now(), '%%Y%%m%%d%%H%%i%%s'))", ptr->tablename);
	} else if(strcmp(optype,"READNEXT") == 0) {
		if(read(ptr, ArgEntry[6].a_address, READNEXT)) {
			//strncpy(ArgEntry[6].a_address,data,ArgEntry[6].a_length);
			STSHORT(0, ArgEntry[7].a_address);
		} else 
			STSHORT(1, ArgEntry[7].a_address);
		return (RM_FND);
	}
	//printf("\n\n%s\n\n",sql);
	
	if (sql == NULL) {
		sprintf(msgbuffer, "[%s] bilinmeyen komut.", optype);
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		mysql_close(conn);
		return (RM_STOP);
	} 
	if(strcmp(optype,"DELETE") == 0) {
		if (mysql_query(conn, sql_log)) {
			sprintf(msgbuffer, "sql:[%s]\n%s", sql, mysql_error(conn));
			MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
			mysql_close(conn);
			return (RM_STOP);
		}
	}
	
    if (mysql_query(conn, sql)) {
        sprintf(msgbuffer, "sql:[%s]\n%s", sql, mysql_error(conn));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		mysql_close(conn);
		return (RM_STOP);
    }
	
	if(strcmp(optype,"DELETE") != 0) {
		if (mysql_query(conn, sql_log)) {
			sprintf(msgbuffer, "sql:[%s]\n%s", sql, mysql_error(conn));
			MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
			mysql_close(conn);
			return (RM_STOP);
		}
	}
	
	return (RM_FND);
}

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

