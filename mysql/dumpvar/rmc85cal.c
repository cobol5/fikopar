#include <windows.h>
#define RMLittleEndian 1
#include "rmc85cal.h"
#include <math.h>
#include <iconv.h>

// get float value from callback value

float getf(ARGUMENT_ENTRY *p) {
	if (p->a_type != RM_NSU && p->a_type != RM_NTC)
		return 0;
	float var = 0;
	int sign = 1;
	for(int i=p->a_length-1; i>=0; i--) {
		short c = (short) p->a_address[i];
		if (c == 123) {            			  // { 0 positive
			sign = 1;
			c = 0;
		} else if (c == 125 ) {	  			  // } 0 negative 
			sign = -1;
			c = 0;
		} else if (c >= 65 && c <= 73) {      // A .. I positive
			sign = 1;
			c = c - 64;
		} else if (c >= 74 && c <= 83) {	  // J .. S negative
			sign = -1;
			c = c - 73;
		} else 
			c = c - 48;
		var += c * pow(10, p->a_length - 1 - i);
	}
	return var * pow(10, (short) p->a_scale) * sign;
}

// put float value into callback value

void putf(float val, ARGUMENT_ENTRY *p) {
	if (p->a_type != RM_NSU && p->a_type != RM_NTC)
		return;
	int sign = val >= 0 ? 1 : -1;
	val *= sign * pow(10, (short)p->a_scale * -1);
	for(int i=0; i<p->a_length; i++) {
		short c = val / pow(10, p->a_length - i - 1);
		val = val - (long) c * pow(10, p->a_length - i - 1);
		p->a_address[i] = c + 48;
	}
	if (p->a_type == RM_NTC) {
		short c = p->a_address[p->a_length - 1] - 48;
		if (c == 0)
			p->a_address[p->a_length - 1] = (sign == 1 ? 123 : 125);
		else
			p->a_address[p->a_length - 1] = c + (sign == 1 ? 64 : 73);
	}
}

// get alphastring value from callback variable

char * geta(ARGUMENT_ENTRY *p) {
	if (p->a_type != RM_ANS)
		return NULL;
	char * d = (char *) calloc(p->a_length*4, sizeof(char));
	memset(d, ' ', p->a_length*4);
	
	char * src = p->a_address;
	char * des = d;
	
	size_t in = p->a_length;
	size_t ou = p->a_length*4;
	
	iconv_t foo = iconv_open("UTF-8", "857");
	iconv(foo, &src, &in, &des, &ou);
	iconv_close(foo);
	
	des = d + p->a_length*4 - 1;
	while(*des == ' ' && d!=des) {
		//*des = '_';
		des--;
	}
	if(des != d) des++;
	*des = '\0';
	
	return d;
}
// put alphastrıng value to callback variable

void puta(char *s, ARGUMENT_ENTRY *p) {
	if (p->a_type != RM_ANS)
		return;
	
	char * d = (char *) calloc(p->a_length, sizeof(char));
	
	char * src = s;
	char * des = d;
	
	size_t in = p->a_length*4;
	size_t ou = p->a_length;
	
	iconv_t foo = iconv_open("857", "UTF-8");
	iconv(foo, &src, &in, &des, &ou);
	iconv_close(foo);
	
	memset(p->a_address, ' ', p->a_length);
	memcpy(p->a_address, d, p->a_length);
	free(d);
}
