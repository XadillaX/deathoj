//////////////////////////////////////////////////////////////////////////  
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 艾克视图(http://www.x2studio.net)
/// All rights reserved.
///
/// @file NBUTOJCore.h
/// @brief 评测核心类头文件
///
/// 声明了dll的核心类CNBUTOJCore以及其它一些琐碎
///  
/// @version 1.0
/// @author 朱凯迪
/// @date 2010-12-7
///  
///  
/// 修订说明：最初版本
//////////////////////////////////////////////////////////////////////////
#ifndef NBUTOJCORE_H
#define NBUTOJCORE_H

#include <string>
#include <cstring>
#include <windows.h>
#include <time.h>
using namespace std;

#define SAFEDEL(p) { if(p){ delete p; p = NULL; } }
#define NAPI NBUTOJCORE_API

#ifdef NBUTOJCORE_EXPORTS
    #define NBUTOJCORE_API __declspec(dllexport)
#else
    #define NBUTOJCORE_API __declspec(dllimport)
#endif

#define RUN_NO_TIME              0                              ///< 运行之后无时间
#define RUN_NO_MEMO              0                              ///< 运行之后无内存
#define COMPILERS_PATH          "compilers\\"                   ///< 编译器路径
#define TEMP_PATH               "tmpdir\\"                      ///< 临时目录

/**
 * @brief 评测状态枚举
 * 所有代码状态，将在前台显示
 */
enum NState {
    QUEUING,                        ///< 评测队列中
    COMPILING,                      ///< 编译中
    RUNNING,                        ///< 运行中
    ACCEPTED,                       ///< 通过
    PRESENTATION_ERROR,             ///< 格式错误
    WRONG_ANSWER,                   ///< 答案错误
    RUNTIME_ERROR,                  ///< 运行时错误
    TIME_LIMIT_EXCEEDED_1,          ///< 运行超时
    TIME_LIMIT_EXCEEDED_2,          ///< 运行超时
    MEMORY_LIMIT_EXCEEDED,          ///< 内存超出
    OUTPUT_LIMIT_EXCEEDED,          ///< 输出过长
    COMPILATION_ERROR,              ///< 编译错误
    COMPILATION_SUC,                ///< 编译通过
    SYSTEM_ERROR,                   ///< 系统错误
    OUT_OF_CONTEST_TIME             ///< 超出比赛时间
};


/**
 * @brief 代码状态结构体
 * 包含了评测状态、内存占用、时间占用、错误信息等。
 */
struct CodeState {
    NState state;                   ///< 状态
    char err_code[10240];           ///< 错误信息
    __int64 exe_time;               ///< 运行时间
    SIZE_T exe_memory;              ///< 运行内存

    CodeState() 
    {
        err_code[0] = '\0';
    };
};

/**
 * @brief 评测内核类
 * NBUTOJ的评测端引擎内核dll的核心类
 */
class NAPI CNBUTOJCore
{
private:
    string m_szProgramPath;
    HANDLE RunCode(const char *exe, const char *input, const char *output, CodeState &cs, HANDLE &hInput, HANDLE &hOutput, PROCESS_INFORMATION &inProcInfo);
    bool WatchCode(const HANDLE hProcess, const __int64 lim_time, const SIZE_T lim_memo, const DWORD lim_size, const char *outputFilename, CodeState &cs, const PROCESS_INFORMATION ProcInfo);
    void ReleaseIOHandle(HANDLE &hInput, HANDLE &hOutput);
    void ClearUp(const char *exe, const char *output, PROCESS_INFORMATION ProcInfo);
    bool OnDebugEvent(DEBUG_EVENT *pEvent);
    __int64 _GetRunTime(HANDLE hProcess);
    DWORD _GetRunMemo(HANDLE hProcess);
    DWORD _GetRunSize(const char *filename);
    bool _ErrExit(const HANDLE hProcess, const DWORD dwProcessID, CodeState &cs, NState state, __int64 exe_time, SIZE_T exe_memory, const char *code = "");
    bool _IsRight(const char *stdoutput, const char *output, CodeState &cs);

public:
	CNBUTOJCore(void);
    ~CNBUTOJCore();

public:
    bool CompileFile(const string type, const string input, const string output, CodeState &cs);
    bool Judge(const char *exe, const char *stdinput, const char *stdoutput, const __int64 lim_time, const SIZE_T lim_memo, CodeState &cs, bool bPause = false);
    string GetCurrentDir();    
};

extern "C" NAPI string GetCurrentDir(void);
extern "C" NAPI CNBUTOJCore* CreateNCore();
extern "C" NAPI bool ReleaseNCore(CNBUTOJCore* &core);
extern "C" NAPI bool FileExists(const char *filename);
extern "C" NAPI char* GetCurrentDirC(void);

extern "C" NAPI bool NBUTOJ_Compile(char* type, char* input, char* output);
extern "C" NAPI int NBUTOJ_GetLastStateCode();
extern "C" NAPI char* NBUTOJ_GetLastError();
extern "C" NAPI int NBUTOJ_GetLastTime();
extern "C" NAPI int NBUTOJ_GetLastMemo();

extern "C" NAPI bool NBUTOJ_Judge(char* exe, char* stdipt, char* stdopt, int lim_time, int lim_memo);

#endif
