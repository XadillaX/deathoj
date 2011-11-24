#include "StdAfx.h"
#include "NCompiler_GPP.h"

NCompiler_GPP::NCompiler_GPP(void)
{
    string gpp_path = string(COMPILERS_PATH) + string(GPP_PATH);

    compiler_format = gpp_path + string(" -o %s %s -ansi -fno-asm -O2 -Wall -lm --static -DONLINE_JUDGE 2> %s\n");
}

NCompiler_GPP::~NCompiler_GPP(void)
{
}

bool NCompiler_GPP::CompileFile(const char *input, const char *output, char *err_code)
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
                //MessageBox(NULL, "", "", MB_OK);
                if(1 == strlen(err_code)) strcpy(err_code, "Unknown error.");
                return false;
            }            
        }
    }
    else return false;
}