#include "StdAfx.h"
#include <io.h>
#include <stdlib.h>
#include "NCompiler.h"

/**
 * @brief 构造函数
 */
NCompiler::NCompiler(void)
{

}

/**
 * @brief 析构函数
 */
NCompiler::~NCompiler(void)
{

}

/**
 * @brief 建批处理函数
 * 创建一个用于编译代码的批处理文件
 *
 * @param cmd 批处理命令行
 * @return 本函数返回建立结果，true为建立成功，false为建立失败
 */
bool NCompiler::MakeBat(const char *cmd)
{
    FILE *fp;
    FILE *lock;                     ///< 编译锁文件
    if(NULL == (fp = fopen(BAT_FILENAME, "w+")) ||
        NULL == (lock = fopen(LOCK_FILENAME, "w+")))
    {
        fclose(fp);
        fclose(lock);
        return false;
    }

    /** 输出编译命令 */
    fprintf(fp, "%s", cmd);

    /** 测试编译锁时的延时指令 */
    //fprintf(fp, "ping 127.0.0.1\n");

    /** 输出删除编译锁命令 */
    fprintf(fp, "del %s\n", LOCK_FILENAME);

    fclose(fp);
    fclose(lock);

    return true;
}

/**
 * @brief 编译函数
 * 虚函数，用于继承
 *
 * @param input 代码文件名
 * @param output 生成文件名
 * @param err_code 用于接收错误信息的字符串
 * @return 本函数返回编译结果，true为编译成功，false为编译失败
 *
 * @see _CompileFile(char *err_code)
 */
bool NCompiler::CompileFile(const char *input, const char *output, char *err_code)
{
    /** Todo: */
    return true;
}

/**
 * @brief 通用编译函数
 * NCompiler及其继承类的通用编译函数
 *
 * @param err_code 用于接收错误信息的字符串
 * @return 本函数返回编译结果，true为编译成功，false为编译失败
 */
bool NCompiler::_CompileFile(char *err_code)
{
    /** 运行bat文件进行编译 */
    HINSTANCE hIns = ShellExecute(NULL,
            "open",
            BAT_FILENAME,
            "",
            "",
            SW_HIDE
        );

    //cout << hIns << endl;
    /** 若ShellExecute返回句柄错误 */
    if(hIns <= (HINSTANCE)(32))
    {
        strcpy(err_code, "System error.");
        return false;
    }

    //time_t t;
    //time(&t);
    //cout << "编译开始时间：" << t << endl;
    
    /** 等待编译锁文件被删除 */
    while(0 == access(LOCK_FILENAME, 0)) Sleep(1);

    //time(&t);
    //cout << "编译结束时间：" << t << endl;

    /** 读取错误信息 */
    FILE *err_file;
    if(NULL == (err_file = fopen(ERR_FILENAME, "r")))
    {
        strcpy(err_code, "System error.");
        return false;
    }

    /** 读取err_file */
    char c[10240];
    int i = 0;
    while(!feof(err_file) && i < 10000)
    {
        c[i++] = fgetc(err_file);
    }
    c[i] = '\0';
    fclose(err_file);

    strcpy(err_code, c);
    ::DeleteFile(ERR_FILENAME);
    ::DeleteFile(BAT_FILENAME);
    //strcpy(err_code, "\0");

    return true;
}

/**
 * @brief 过滤函数
 * 过滤不安全的代码
 *
 * @param *input 代码文件名
 * @param *filter_filename 过滤规则文件名
 * @return 返回是否过滤（即不安全代码），true为被过滤，false为没被过滤
 *
 * @note 虚函数，用于继承
 */
bool NCompiler::FilterCode(const char *input)
{

    return false;
}