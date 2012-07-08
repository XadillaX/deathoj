#include "StdAfx.h"
#include "NCompiler_GCC.h"

NCompiler_GCC::NCompiler_GCC(void)
{
    string gcc_path = string(COMPILERS_PATH) + string(GCC_PATH);

    compiler_format = gcc_path + string(" -o %s -x c %s -ansi -fno-asm -O2 -Wall -lm --static -DONLINE_JUDGE 2> %s\n");
}

NCompiler_GCC::~NCompiler_GCC(void)
{
}

bool NCompiler_GCC::CompileFile(const char *input, const char *output, char *err_code)
{
    char cmd[1024];
    sprintf(cmd, compiler_format.c_str(), output, input, ERR_FILENAME);
    
    /** �������ڱ�����������ļ� */
    if(MakeBat(cmd))
    {
        /** ͨ�ñ��� */
        if(!_CompileFile(err_code))
        {
            /** ����ϵͳ���� */
            return false;
        }
        else
        {
            /** �������� */
            if(FileExists(output))
            {
                return true;
            }
            else
            {
                if(1 == strlen(err_code)) strcpy(err_code, "Unknown error.");
                return false;
            }            
        }
    }
    else return false;
}

#include <string>
using namespace std;
bool NCompiler_GCC::FilterCode(const char* input)
{
    FILE* fp = fopen(input, "r");
    if(NULL == fp) return false;

    string code;
    char ch;
    while(fscanf(fp, "%c", &ch) != EOF)
    {
        if(ch >= 'A' && ch <= 'Z') ch = ch - 'A' + 'a';
        code += ch;
    }

    fclose(fp);

    /** ��ʼ���� */
    int FILTER_N = 18;
    string filter[] = {
        "windows",
        "system",
        "pause",
        "winbase",
        "winsock",
        "hinstance",
        "openprocess",
        //"fopen",
        //"fclsoe",
        //"fread",
        //"remove",
        "winapi",
        //"fwrite",
        //"fscanf",
        //"fgets",
        //"fputc",
        //"fgetc",
        //"fseek"
    };

    for(int i = 0; i < FILTER_N; i++)
    {
        if(code.find(filter[i]) != string::npos)
        {
            return true;
        }
    }

    return false;
}
