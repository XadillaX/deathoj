//////////////////////////////////////////////////////////////////////////
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 艾克视图(http://www.x2studio.net)
/// All rights reserved.
///
/// @file NCompiler.h
/// @brief 编译器基类头文件
///
/// 声明了编译器基类NCompiler
///  
/// @version 1.0
/// @author 朱凯迪
/// @date 2010-12-7
///  
///  
/// 修订说明：最初版本
//////////////////////////////////////////////////////////////////////////
#ifndef NCOMPILER_H
#define NCOMPILER_H

#pragma once

#define ERR_FILENAME        "err.txt"           ///< 输出错误文件名
#define BAT_FILENAME        "make.bat"          ///< 批处理文件名
#define LOCK_FILENAME       ".lock"             ///< 编译锁文件名

/**
 * @brief 编译器基类
 * 提供编译函数的基础类，
 * 供各语言的编译类继承。
 */
class NCompiler
{
protected:
    string compiler_format;                     ///< 编译指令规则，用于sprintf
    bool MakeBat(const char *cmd);
    bool _CompileFile(char *err_code);

public:
    NCompiler(void);
    virtual ~NCompiler(void);

    virtual bool CompileFile(const char *input, const char *output, char *err_code);
    virtual bool FilterCode(const char *input, const char *filter_filename);
};

#endif
