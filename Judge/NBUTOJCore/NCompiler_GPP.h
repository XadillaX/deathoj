#pragma once
#include "ncompiler.h"

#define GPP_PATH                "C\\bin\\g++.exe"               ///< C++Â·¾¶

class NCompiler_GPP : public NCompiler
{
public:
    NCompiler_GPP(void);
    ~NCompiler_GPP(void);

    virtual bool CompileFile(const char *input, const char *output, char *err_code);
};
