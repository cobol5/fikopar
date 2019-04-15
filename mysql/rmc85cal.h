#ifndef _RMC85CAL_H_
#define _RMC85CAL_H_
/* 
 *	Title:	rmc85cal.h 
 *		RM/COBOL Machine Language Calling Interface 
 * *	Copyright (c) 2000 Liant Software Corporation.	All rights reserved. * 
 *	Version = @(#) $Revision:   6.6.1.1  $ $Date:   21 Sep 1999 12:19:26  $ 
 */

/* Machine language subroutine header */

struct PROCTABLE{    
	char *subname;		/* name of subroutine as in call */    
	int (*ent_pnt)();	/* pointer to subroutine function */
};

/* Subroutine argument structure */

#ifndef _INC_WINDOWS

/* -- if not windows */
struct ARGUMENT_ENTRY {
	char	*a_address;		/* pointer to start of arg */
	unsigned long a_length;	/* length of argument */		
	short a_type;			/* Arg type See Table Below */
	char a_digits;			/* digit count 0x1-0x12 */
	char a_scale;			/* implied decimal location */
	char *a_picture;		/* pointer to edit picture */
};
#else

/* -- if windows */

#if defined(_MSC_VER) && _MSC_VER >= 1100
#pragma pack(push,1)
#endif

typedef struct ArgumentEntry{
	char FAR *a_address;	/* pointer to start of arg */
	unsigned long a_length;	/* length of argument */
	short a_type;			/* Arg type See Table Below */
	char a_digits;			/* digit count 0x1-0x12 */
	char a_scale;			/* implied decimal location */
	char FAR *a_picture;	/* pointer to edit picture */
} ARGUMENT_ENTRY;

#if defined(_MSC_VER) && _MSC_VER >= 1100
#pragma pack(pop)
#endif


#ifdef RMLittleEndian

/* -- The following macros differ from the ones in standdef.h.	They
   -- contain FAR pointer casts and are intended for windows (dll) use only. 
 */

#define LDSHORT(p) (((short)((unsigned char FAR *)(p))[0]<<8) | \
		(((unsigned char FAR *)(p))[1]))

#define STSHORT(i,p) (((unsigned char FAR *)(p))[0]=(char)(((unsigned short)(i))>>8),\
		((unsigned char FAR *)(p))[1]=(char)(i))

#define LDLONG(p) (((short)((char FAR *)(p))[0]<<24) | \
		((short)((char FAR *)(p))[1]<<16) | \
		((short)((char FAR *)(p))[2]<<8) | \
		((short)((char FAR *)(p))[3]))

#define STLONG(i,p) (((unsigned char FAR *)(p))[0]=(char)(((unsigned long)(i))>>24),\
		((unsigned char FAR *)(p))[1]=(char)(((unsigned long)(i))>>16),\
		((unsigned char FAR *)(p))[2]=(char)(((unsigned short)(i))>>8),\
		((unsigned char FAR *)(p))[3]=(char)(((unsigned short)(i))))

#endif

#ifdef RMBigEndian
#define LDSHORT(p) (*((short *)(p)))
#define STSHORT(i,p) (*((short *)(p))=(short)(i))
#define LDLONG(p) (*((long *)(p)))#define STLONG(i,p) (*((long *)(p))=(long)(i))
#endif
#endif
/* CALL return values */
#define RM_FND 0
#define RM_NFND -1
#define RM_STOP 1

/* Argument type definitions 
 * * use as if (arg_tbl.argument_type == RM_xxx) *
 */
#define RM_NSE 0		/* Numeric edited */
#define RM_NSU	1		/* Numeric display unsigned */
#define RM_NTS	2		/* Numeric display trailing seperate */
#define RM_NTC	3		/* Numeric display trailing combined */
#define RM_NLS	4		/* Numeric display leading seperate */
#define RM_NLC	5		/* Numeric display leading combined */
#define RM_NCS	6		/* Numeric unpacked signed */
#define RM_NCU	7		/* Numeric unpacked unsigned */
#define RM_NPP	8		/* Packed unsigned */
#define RM_NPS	9		/* Packed signed */
#define RM_NPU	10		/* Numeric packed unsigned */
#define RM_NBS	11		/* Binary signed */
#define RM_NBU	12		/* Binary unsigned or index */
#define RM_ANS	16		/* Alphanumeric */
#define RM_ANSR 17		/* Alphanumeric right justified */
#define RM_ABS	18		/* Alphabetic */
#define RM_ABSR 19		/* Alphabetic right justified */
#define RM_ANSE 20		/* Alphanumeric edited */
#define RM_GRPF 22		/* Group fixed length */
#define RM_PTR	25		/* Pointer */
#define RM_OMITTED 32	/* omitted parameter */
#endif
