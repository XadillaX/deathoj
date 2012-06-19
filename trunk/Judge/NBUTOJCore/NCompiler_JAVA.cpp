#include "StdAfx.h"
#include "NCompiler_JAVA.h"

NCompiler_JAVA::NCompiler_JAVA(void)
{
    string javac_path = string(COMPILERS_PATH) + string(JAVAC_PATH);
    compiler_format = javac_path + string(" -J-Xms32m -J-Xmx256m %s > %s\n");
}

NCompiler_JAVA::~NCompiler_JAVA(void)
{
}

bool NCompiler_JAVA::CompileFile(const char *input, const char *output, char *err_code)
{
    char cmd[1024];
    sprintf(cmd, compiler_format.c_str(), input, ERR_FILENAME);

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
