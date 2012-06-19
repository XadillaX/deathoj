#include "StdAfx.h"
#include "NCompiler_FPC.h"

NCompiler_FPC::NCompiler_FPC(void)
{
    string gcc_path = string(COMPILERS_PATH) + string(FPC_PATH);

    compiler_format = gcc_path + string(" %s -Sd -o%s -O2 -Op2 -dONLINE_JUDGE > %s\n");

}

NCompiler_FPC::~NCompiler_FPC(void)
{
}

bool NCompiler_FPC::CompileFile(const char *input, const char *output, char *err_code)
{
    char cmd[1024];
    sprintf(cmd, compiler_format.c_str(), input, output, ERR_FILENAME);

    /** �������ڱ�����������ļ� */
    if(MakeBat(cmd))
    {
        //system("pause");
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
bool NCompiler_FPC::FilterCode(const char* input)
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
    int FILTER_N = 5;
    string filter[] = {
        "windows",
        "system",
        "pause",
        "assign",
        "exec"
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
