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
    
    /** 建议用于编译的批处理文件 */
    if(MakeBat(cmd))
    {
        /** 通用编译 */
        if(!_CompileFile(err_code))
        {
            /** 若是系统错误 */
            return false;
        }
        else
        {
            /** 分析错误 */
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
