//////////////////////////////////////////////////////////////////////////  
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 艾克视图(http://www.x2studio.net)
/// All rights reserved.
///
/// @file NCompiler_FPC.h
/// @brief Free Pascal编译类头文件
///
/// 声明FPC的编译类
///  
/// @version 1.0
/// @author 朱凯迪
/// @date 2012-6-11
///  
///  
/// 修订说明：最初版本
//////////////////////////////////////////////////////////////////////////
#pragma once
#include "ncompiler.h"

#define FPC_PATH                "FPC\\2.4.4\\bin\\i386-win32\\fpc.exe"               ///< C++路径

class NCompiler_FPC :
    public NCompiler
{
public:
    NCompiler_FPC(void);
    ~NCompiler_FPC(void);

    virtual bool CompileFile(const char *input, const char *output, char *err_code);
    virtual bool FilterCode(const char* input);
};
