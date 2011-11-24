//////////////////////////////////////////////////////////////////////////  
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 ������ͼ(http://www.x2studio.net)
/// All rights reserved.
///
/// @file NCompiler_GCC.h
/// @brief GCC������ͷ�ļ�
///
/// ����GCC�ı�����
///  
/// @version 1.0
/// @author �쿭��
/// @date 2010-12-7
///  
///  
/// �޶�˵��������汾
//////////////////////////////////////////////////////////////////////////
#pragma once
#include "ncompiler.h"

#define GCC_PATH                "C\\bin\\gcc.exe"               ///< C++·��

class NCompiler_GCC : public NCompiler
{
public:
    NCompiler_GCC(void);
    ~NCompiler_GCC(void);

    virtual bool CompileFile(const char *input, const char *output, char *err_code);
};
