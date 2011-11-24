//////////////////////////////////////////////////////////////////////////
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 ������ͼ(http://www.x2studio.net)
/// All rights reserved.
///
/// @file NCompiler.h
/// @brief ����������ͷ�ļ�
///
/// �����˱���������NCompiler
///  
/// @version 1.0
/// @author �쿭��
/// @date 2010-12-7
///  
///  
/// �޶�˵��������汾
//////////////////////////////////////////////////////////////////////////
#ifndef NCOMPILER_H
#define NCOMPILER_H

#pragma once

#define ERR_FILENAME        "err.txt"           ///< ��������ļ���
#define BAT_FILENAME        "make.bat"          ///< �������ļ���
#define LOCK_FILENAME       ".lock"             ///< �������ļ���

/**
 * @brief ����������
 * �ṩ���뺯���Ļ����࣬
 * �������Եı�����̳С�
 */
class NCompiler
{
protected:
    string compiler_format;                     ///< ����ָ���������sprintf
    bool MakeBat(const char *cmd);
    bool _CompileFile(char *err_code);

public:
    NCompiler(void);
    virtual ~NCompiler(void);

    virtual bool CompileFile(const char *input, const char *output, char *err_code);
    virtual bool FilterCode(const char *input, const char *filter_filename);
};

#endif
