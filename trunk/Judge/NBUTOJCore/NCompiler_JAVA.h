//////////////////////////////////////////////////////////////////////////  
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 艾克视图(http://www.x2studio.net)
/// All rights reserved.
///
/// @file NCompiler_GCC.h
/// @brief GCC编译类头文件
///
/// 声明GCC的编译类
///  
/// @version 1.0
/// @author 朱凯迪
/// @date 2010-12-7
///  
///  
/// 修订说明：最初版本
//////////////////////////////////////////////////////////////////////////
#pragma once
#include "ncompiler.h"

#define JAVAC_PATH                  "JDK\\bin\\javac.exe"               ///< JAVAC路径
#define JAVA_PATH                   "JDK\\bin\\java.exe"                ///< JAVA路径

class NCompiler_JAVA :
    public NCompiler
{
public:
    NCompiler_JAVA(void);
    ~NCompiler_JAVA(void);

    virtual bool CompileFile(const char *input, const char *output, char *err_code);
};
