#include <windows.h>
#include "rmc85cal.h"
#include <mysql.h>
#include <stdio.h>

int replace_str(char *str, char *orig, char *rep)
{
  char *p;

  if(!(p = strstr(str, orig)))  // Is 'orig' even in 'str'?
    return -1;
  char *rem = (char *) calloc(p-str+1, sizeof(char));
  strncpy(rem, p+strlen(orig), p-str+1);
  strncpy(p, rep, strlen(rep));
  strncpy(p+strlen(rep), rem, strlen(rem)+1);
 
  free(rem);
 
  return 1;
}


int __declspec(dllexport)  __cdecl exec_scaler(char *Name, WORD ArgCount, ARGUMENT_ENTRY *ArgEntry, WORD State) {
	
	MYSQL *mysql_conn = NULL;
	my_bool mysql_reconnect = 1;
	char *mysql_charset = "utf8";
	char *mysql_autocommit = "SET autocommit=0";

	/*
	 *  0 -> mysql_server_host
	 *  1 -> username
	 *  2 -> password
	 *  3 -> query
	 *  4 -> data
	 */
	int _DEBUG_ = (int) getf(&ArgEntry[0]);
	
	if (_DEBUG_) {
		AllocConsole();
		freopen("CONIN$", "r",stdin);
		freopen("CONOUT$", "w",stdout);
		freopen("CONOUT$", "w",stderr); 
	}
	 
	char * server = geta(&ArgEntry[1]);
	char * user = geta(&ArgEntry[2]);
	char * password = geta(&ArgEntry[3]);
	char * query = geta(&ArgEntry[4]);
	char * rep = (char *) calloc(10, sizeof(char));
	
	char * param; float fparam;
	for(WORD i=5; i < ArgCount; i++) {
		sprintf(rep,":%u\0", i-4);
		param = geta(&ArgEntry[i]);
		if(!param) {
			fparam = getf(&ArgEntry[i]);
			param = (char *) calloc(100, sizeof(char)); 
			sprintf(param,"%f\0",fparam);
		}
		if(replace_str(query, rep, param) == -1) { free(param); break; }
		free(param);
	}
	// update query.
	puta(query, &ArgEntry[4]);
	
	char msgbuffer[2048];
	
	MYSQL_RES *res;
	MYSQL_ROW row;
	MYSQL_FIELD *field;
	int num_fields, i;
	
	if(server ==NULL || user == NULL || password == NULL || query == NULL)
		return (RM_STOP);
	
	
	if (_DEBUG_)  {
		printf("Name	: [%s]\n", Name);
		printf("ArgCount: [%d]\n", ArgCount);
		printf("State	: [%d]\n", State);
	
		printf("server	: [%s]\n", server);
		printf("user    : [%s]\n", user);
		printf("password: [%s]\n", password);
		printf("query	: [%s]\n", query);
	}
	
	if(mysql_conn == NULL) {
		
		mysql_conn = mysql_init(NULL);
		
		
		mysql_options(mysql_conn, MYSQL_SET_CHARSET_NAME, mysql_charset);
		mysql_options(mysql_conn, MYSQL_OPT_RECONNECT, &mysql_reconnect);
		mysql_options(mysql_conn, MYSQL_OPT_CONNECT_TIMEOUT, "60");
		mysql_options(mysql_conn, MYSQL_INIT_COMMAND, mysql_autocommit);
		
		
		if (!mysql_real_connect(mysql_conn, server, user, password, NULL, 0, NULL, 0)) {
			sprintf(msgbuffer, "%s", mysql_error(mysql_conn));
			MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
			return (RM_STOP);
		}
		
	}
	
	if (mysql_query(mysql_conn, query)) {
        sprintf(msgbuffer, "%s", mysql_error(mysql_conn));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		return (RM_STOP);
    }
	res = mysql_use_result(mysql_conn);
	
	num_fields = mysql_num_fields(res);

	while ((row = mysql_fetch_row(res))) 
	{ 
	  int j = 1;
      for(int i = num_fields - 1; i >= 0; i--) 
      {
		  if (_DEBUG_) 
			printf("%d: %s\n", i, row[i] );
		  puta((char *) row[i], &ArgEntry[ArgCount-j]);
		  putf(atof(row[i]), &ArgEntry[ArgCount-j]);                                                  
		  j++;
      } 
      printf("\n"); 
	}
	
	mysql_free_result(res);
    
	mysql_close(mysql_conn);
	return (RM_FND);
	
}
