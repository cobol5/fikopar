#include <windows.h>
#include <mysql.h>
#include <stdio.h>


MYSQL *conn = NULL;
my_bool reconnect = 1;
char *charset = "utf8";
char *autocommit = "SET autocommit=0";

int main(int argc, char **argv)
{
	//printf("MySQL client version: %s\n", mysql_get_client_info());
  
	char * server = "diesel.dyndns.biz";
	char * user = "root";
	char * password = "1413";
	char * query = "select * from windows.win_alimdetay where stno = '031708'";
	char msgbuffer[2048];
	
	MYSQL_RES *res;
	MYSQL_ROW row;
	MYSQL_FIELD *field;
	//int num_fields, i;
	
	conn = mysql_init(conn);
		
	mysql_options(conn, MYSQL_SET_CHARSET_NAME, charset);
	mysql_options(conn, MYSQL_OPT_RECONNECT, &reconnect);
	mysql_options(conn, MYSQL_OPT_CONNECT_TIMEOUT, "60");
	mysql_options(conn, MYSQL_INIT_COMMAND, autocommit);
		
	if (!mysql_real_connect(conn, server, user, password, NULL, 0, NULL, 0)) {
		sprintf(msgbuffer, "%s", mysql_error(conn));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		exit (0);
	}
    
	if (mysql_query(conn, query)) {
        sprintf(msgbuffer, "%s", mysql_error(conn));
		MessageBox( 0, msgbuffer, "MySQL Hata", MB_OK );
		exit (0);
    }
	res = mysql_use_result(conn);
	
	int num_fields = mysql_num_fields(res);

	while ((field = mysql_fetch_field(res))) 
	{ 
      printf("field name     : %s \n", field->name); 
	  printf("field length   : %d \n", field->length); 
	  printf("field decimals : %d \n", field->decimals); 
	  printf("field charset  : %d \n\n", field->charsetnr); 
	}
	
	
	while ((row = mysql_fetch_row(res))) 
	{ 
      for(int i = 0; i < num_fields; i++) 
      { 
          printf("%s ", row[i] ? row[i] : "NULL"); 
      } 
      printf("\n"); 
	}
	
	mysql_free_result(res);

    
	mysql_close(conn);
	
	exit(0);
}

