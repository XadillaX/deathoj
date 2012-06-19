//////////////////////////////////////////////////////////////////////////  
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 ������ͼ(http://www.x2studio.net)
/// All rights reserved.
///
/// @file NCompiler_FPC.h
/// @brief Free Pascal������ͷ�ļ�
///
/// ����FPC�ı�����
///  
/// @version 1.0
/// @author �쿭��
/// @date 2012-6-11
///  
///  
/// �޶�˵��������汾
//////////////////////////////////////////////////////////////////////////
#pragma once
#include "ncompiler.h"

#define FPC_PATH                "FPC\\2.4.4\\bin\\i386-win32\\fpc.exe"               ///< C++·��

class NCompiler_FPC :
    public NCompiler
{
public:
    NCompiler_FPC(void);
    ~NCompiler_FPC(void);

    virtual bool CompileFile(const char *input, const char *output, char *err_code);
    virtual bool FilterCode(const char* input);
};
