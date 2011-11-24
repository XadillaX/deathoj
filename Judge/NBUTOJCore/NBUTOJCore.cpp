#include "stdafx.h"
#include "CPFactories.h"
#include "NBUTOJCore.h"
#include <io.h>
#include <stdlib.h>
#include <FCNTL.h>
#include <Psapi.h>
#include <sys/stat.h>
#include <Tlhelp32.h>

#pragma comment (lib, "psapi.lib")

CNBUTOJCore Alpha_NBUTOJCore;
CodeState Alpha_cs;

/** 非类函数 */
CNBUTOJCore* CreateNCore()
{
    return new CNBUTOJCore();
}

bool ReleaseNCore(CNBUTOJCore* &core)
{
    SAFEDEL(core);
    
    return true;
}

string GetCurrentDir()
{
    return Alpha_NBUTOJCore.GetCurrentDir();
}

char* GetCurrentDirC(void)
{
    static char dir[1024];
    memcpy(dir, GetCurrentDir().c_str(), GetCurrentDir().length() + 1);

    return dir;
}

bool NBUTOJ_Compile(char* type, char* input, char* output)
{
    bool rst = Alpha_NBUTOJCore.CompileFile(type, input, output, Alpha_cs);

    return rst;
}

bool NBUTOJ_Judge(char* exe, char* stdipt, char* stdopt, int lim_time, int lim_memo)
{
    return Alpha_NBUTOJCore.Judge(exe, stdipt, stdopt, lim_time, lim_memo, Alpha_cs);
}

int NBUTOJ_GetLastStateCode()
{
    return Alpha_cs.state;
}

char* NBUTOJ_GetLastError()
{
    return Alpha_cs.err_code;
}

int NBUTOJ_GetLastTime()
{
    return Alpha_cs.exe_time;
}

int NBUTOJ_GetLastMemo()
{
    return Alpha_cs.exe_memory;
}

bool FileExists(const char *filename)
{
    return (0 == access(filename, 0));
}

/** 类成员函数 */
CNBUTOJCore::CNBUTOJCore()
{
    char dir[512];
    GetCurrentDirectory(512 * sizeof(char), dir);
    m_szProgramPath = string(dir);
	
    return;
}

CNBUTOJCore::~CNBUTOJCore()
{
    /** Todo: 析构函数 */
}

/**
 * @brief 编译函数
 * 将代码文件根据对应编译器编译成可执行文件
 * 
 * @param type 编译器类型
 * @param input 代码文件名
 * @param output 可执行文件名
 * @param cs 用于接收代码状态的引用
 * @return 若编译成功返回true，否则返回false
 */
bool CNBUTOJCore::CompileFile(const string type, const string input, const string output, CodeState &cs)
{
    /** 根据编译器类型创建编译对象 */
    NCompiler *cp = CPFactories::Instance().Create(type);
    
    /** 若找不到相应编译器 */
    if(!cp)
    {
        cs.state = SYSTEM_ERROR;
        strcpy(cs.err_code, "Wrong Compiler.");
        return false;
    }

    /** 获取新路径 */
    string newinput = string(TEMP_PATH) + input;
    string newoutput = string(TEMP_PATH) + output;

    /** 编译文件 */
    bool r = cp->CompileFile(newinput.c_str(), newoutput.c_str(), cs.err_code);
    
    /** 更新状态 */
    if(r) 
    {
        cs.state = COMPILATION_SUC;
        strcpy(cs.err_code, "\0");
    }
    else cs.state = COMPILATION_ERROR;

    /** 删除编译器对象指针 */
    SAFEDEL(cp);

    return r;
}

/**
 * @brief 运行进程函数
 * 创建经编译的代码的可执行文件进程
 *
 * @param exe 可执行文件文件名
 * @param input 进程输入文件名
 * @param output 进程输出文件名
 * @param cs 用于接收代码状态的引用
 * @param hInput 用于接收输入文件句柄
 * @param hOutput 用于接收输出文件句柄
 * @return 若进程创建成功则返回进程句柄，否则返回NULL
 */
HANDLE CNBUTOJCore::RunCode(const char *exe, const char *input, const char *output, CodeState &cs, HANDLE &hInput, HANDLE &hOutput, PROCESS_INFORMATION &inProcInfo)
{
    SECURITY_ATTRIBUTES sa;
    sa.bInheritHandle = true;
    sa.nLength = sizeof(sa);
    sa.lpSecurityDescriptor = NULL;

    /** 打开得到输入输出文件句柄 */
    hInput = hOutput = NULL;
    hInput = CreateFile(input, GENERIC_READ, NULL, &sa, OPEN_EXISTING,
        FILE_ATTRIBUTE_NORMAL, NULL);
    hOutput = CreateFile(output, GENERIC_WRITE | GENERIC_READ, NULL,
        &sa, OPEN_ALWAYS, FILE_ATTRIBUTE_NORMAL, NULL);
    //HANDLE hError = CreateFile(".err", GENERIC_WRITE | GENERIC_READ, NULL,
    //    &sa, OPEN_ALWAYS, FILE_ATTRIBUTE_NORMAL, NULL);
    if(NULL == hInput || NULL == hOutput)
    {
        cs.state = SYSTEM_ERROR;
        strcpy(cs.err_code, "File error.");

        return NULL;
    }

    PROCESS_INFORMATION ProcInfo;
    STARTUPINFO StartInfo = { sizeof(StartInfo) };
    StartInfo.cb = sizeof(StartInfo);
    StartInfo.dwFlags = STARTF_USESTDHANDLES | STARTF_USESHOWWINDOW;
    StartInfo.hStdInput = hInput;
    StartInfo.hStdOutput = hOutput;
    StartInfo.wShowWindow = SW_HIDE;
    //StartInfo.hStdError = hError;

    /** 创建批处理文件进程 */
    bool flag = CreateProcessA(exe, NULL, NULL,
        NULL, true, DEBUG_ONLY_THIS_PROCESS, NULL, NULL, &StartInfo, &ProcInfo);

    /** 若运行不成功 */
    if(!flag)
    {
        cs.state = SYSTEM_ERROR;
        strcpy(cs.err_code, "Can't create process.");

        return NULL;
    }

    cs.state = RUNNING;
    inProcInfo = ProcInfo;

    return ProcInfo.hProcess;
}

bool CNBUTOJCore::OnDebugEvent(DEBUG_EVENT *pEvent)
{

    return true;
}

bool CNBUTOJCore::_ErrExit(const HANDLE hProcess, const DWORD dwProcessID, CodeState &cs, NState state, __int64 exe_time, SIZE_T exe_memory, const char *code)
{
    DebugActiveProcessStop(dwProcessID);
    TerminateProcess(hProcess, 4);
    cs.state = state;
    //cs.exe_time = exe_time;
    cs.exe_time = _GetRunTime(hProcess);
    cs.exe_memory = exe_memory;
    if(strlen(code) != 0)
    {
        strcpy(cs.err_code, code);
    }

    return false;
}

__int64 CNBUTOJCore::_GetRunTime(HANDLE hProcess)
{
    _FILETIME                   CreateTime, ExitTime, KernelTime, UserTime;
    __int64                     *pKT, *pUT, *pCT, *pET;

    if(GetProcessTimes(hProcess, &CreateTime, &ExitTime, &KernelTime, &UserTime))
    {
        //pKT = reinterpret_cast<__int64 *>(&KernelTime);
        pUT = reinterpret_cast<__int64 *>(&UserTime);
        //pCT = reinterpret_cast<__int64 *>(&CreateTime);
        //pET = reinterpret_cast<__int64 *>(&ExitTime);

        return (__int64)((*pUT) / 10000);
    }
    else return -1;
}

DWORD CNBUTOJCore::_GetRunMemo(HANDLE hProcess)
{
    static PROCESS_MEMORY_COUNTERS     memoCounter;
    memoCounter.cb = sizeof(PROCESS_MEMORY_COUNTERS);

    if(GetProcessMemoryInfo(hProcess, &memoCounter, sizeof(PROCESS_MEMORY_COUNTERS)))
    {
        return memoCounter.PagefileUsage / 1024;
    }
    else return -1;
}

DWORD CNBUTOJCore::_GetRunSize(const char* filename)
{
    static struct _stat outputInfo;
    _stat(filename, &outputInfo);

    return outputInfo.st_size;
}

/**
 * @brief 运行监视函数
 * 监视进程运行状态、时间、内存以及输出等信息
 *
 * @param hProcess 进程句柄
 * @param lim_time 时间限制
 * @param lim_memo 内存限制
 * @param lim_size 输出文件长度限制
 * @param cs 用于接收代码状态的引用
 * @return 若运行期间未出现异常则返回true，否则返回false
 */
bool CNBUTOJCore::WatchCode(const HANDLE hProcess, const __int64 lim_time, const SIZE_T lim_memo,
                            const DWORD lim_size, const char *outputFilename, CodeState &cs, const PROCESS_INFORMATION ProcInfo)
{
    DWORD                       maxMemo = 0;                    ///< 使用的内存
    __int64                     RunTime = -1;                   ///< 使用的时间
    DWORD                       nowSize;                        ///< 使用的输出大小
    DEBUG_EVENT                 DBE;                            ///< 调试信息结构体

    while(true)
    {
        /** 继续调试（时间是为了忽略断点的） */
        bool flag = WaitForDebugEvent(&DBE, lim_time + 200);
        if(!flag)
        {
            RunTime = _GetRunTime(hProcess) + lim_time + 200;
            return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, TIME_LIMIT_EXCEEDED_2, RunTime, maxMemo);
        }

        /** 获取运行时间 */
        RunTime = _GetRunTime(hProcess);
        if(-1 == RunTime)
            return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, SYSTEM_ERROR, RunTime, maxMemo, "Can't get running time.");

        /** 获取运行内存 */
        DWORD memo = _GetRunMemo(hProcess);
        if(-1 == memo) return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, SYSTEM_ERROR, RunTime, maxMemo, "Can't get occupied memory.");
        else maxMemo = (memo > maxMemo) ? memo : maxMemo;

        /** 获取运行文件输出大小 */
        nowSize = _GetRunSize(outputFilename);

        /** 若超时 */
        if(RunTime > lim_time)
            return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, TIME_LIMIT_EXCEEDED_1, RunTime, maxMemo);
        
        //cout << RunTime << " " << maxMemo << " " << nowSize << " " << DBE.dwDebugEventCode << endl;

        /** 若超内存 */
        if(maxMemo > lim_memo)
            return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, MEMORY_LIMIT_EXCEEDED, RunTime, maxMemo);

        /** 若超输出 */
        //if(nowSize > lim_size)
        //    return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, OUTPUT_LIMIT_EXCEEDED, RunTime, maxMemo);

        if(DBE.dwDebugEventCode == EXIT_PROCESS_DEBUG_EVENT)
        {
            ContinueDebugEvent(DBE.dwProcessId, DBE.dwThreadId, DBG_EXCEPTION_NOT_HANDLED);
            CloseHandle(DBE.u.CreateProcessInfo.hFile);
            CloseHandle(DBE.u.CreateProcessInfo.hProcess);
            CloseHandle(DBE.u.CreateProcessInfo.hThread);
            break;
        }
        else
        if(DBE.dwDebugEventCode == EXCEPTION_DEBUG_EVENT)
        {
            if(DBE.u.Exception.ExceptionRecord.ExceptionCode != 0x80000003)
            {
                //printf("0x%x\n", DBE.u.Exception.ExceptionRecord.ExceptionCode);
                /** 复制错误 */
                switch(DBE.u.Exception.ExceptionRecord.ExceptionCode)
                {
                case EXCEPTION_ACCESS_VIOLATION:
                    {
                        strcpy(cs.err_code, "ACCESS_VIOLATION");
                        break;
                    }

                case EXCEPTION_ARRAY_BOUNDS_EXCEEDED:
                    {
                        strcpy(cs.err_code, "ARRAY_BOUNDS_EXCEEDED");
                        break;
                    }

                case EXCEPTION_FLT_DENORMAL_OPERAND:
                    {
                        strcpy(cs.err_code, "FLOAT_DENORMAL_OPERAND");
                        break;
                    }

                case EXCEPTION_FLT_DIVIDE_BY_ZERO:
                    {
                        strcpy(cs.err_code, "FLOAT_DIVIDE_BY_ZERO");
                        break;
                    }

                case EXCEPTION_FLT_OVERFLOW:
                    {
                        strcpy(cs.err_code, "FLOAT_OVERFLOW");
                        break;
                    }

                case EXCEPTION_FLT_UNDERFLOW:
                    {
                        strcpy(cs.err_code, "FLOAT_UNDERFLOW");
                        break;
                    }

                case EXCEPTION_INT_DIVIDE_BY_ZERO:
                    {
                        strcpy(cs.err_code, "INTEGER_DIVIDE_BY_ZERO");
                        break;
                    }

                case EXCEPTION_INT_OVERFLOW:
                    {
                        strcpy(cs.err_code, "INTEGER_OVERFLOW");
                        break;
                    }

                case EXCEPTION_STACK_OVERFLOW:
                    {
                        strcpy(cs.err_code, "STACK_OVERFLOW");
                        break;
                    }

                default:
                    {
                        strcpy(cs.err_code, "OTHER_ERRORS");
                        break;
                    }
                }

                /** 关闭句柄，否则调试进程不能被结束 */
                CloseHandle(DBE.u.CreateProcessInfo.hFile);
                CloseHandle(DBE.u.CreateProcessInfo.hProcess);
                CloseHandle(DBE.u.CreateProcessInfo.hThread);
                return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, RUNTIME_ERROR, RunTime, maxMemo);
            }
        }

        /** 继续调试 */
        ContinueDebugEvent(DBE.dwProcessId, DBE.dwThreadId, DBG_EXCEPTION_NOT_HANDLED);
        CloseHandle(DBE.u.CreateProcessInfo.hFile);
        CloseHandle(DBE.u.CreateProcessInfo.hProcess);
        CloseHandle(DBE.u.CreateProcessInfo.hThread);
    }

    cs.exe_time = RunTime;
    cs.exe_memory = maxMemo;

    return true;
}

/**
 * @brief 释放IO句柄函数
 * 释放原先打开的IO句柄
 *
 * @param hInput 输入文件句柄
 * @param hOutput 输出文件句柄
 */
void CNBUTOJCore::ReleaseIOHandle(HANDLE &hInput, HANDLE &hOutput)
{
    CloseHandle(hInput);
    CloseHandle(hOutput);
}

void CNBUTOJCore::ClearUp(const char *exe, const char *output, PROCESS_INFORMATION ProcInfo)
{
    string tmp;

    DebugActiveProcessStop(ProcInfo.dwProcessId);
    TerminateProcess(ProcInfo.hProcess, 0);
    CloseHandle(ProcInfo.hThread);
    CloseHandle(ProcInfo.hProcess);

    while(FileExists(exe))
    {
        //DebugActiveProcessStop(ProcInfo.dwProcessId);
        //TerminateProcess(ProcInfo.hProcess, 0);
        tmp = string("del ") + exe;
        system(tmp.c_str());
    }

    while(FileExists(output))
    {
        tmp = string("del ") + output;
        system(tmp.c_str());
    }
}

bool CNBUTOJCore::_IsRight(const char *stdoutput, const char *output, CodeState &cs)
{
    Sleep(500);
    DWORD stdsize = _GetRunSize(stdoutput);
    DWORD newsize = _GetRunSize(output);
    //cout << stdsize << " " << newsize << endl;
    if(stdsize * 2 < newsize)
    {  
        cs.state = OUTPUT_LIMIT_EXCEEDED;
        return false;
    }

    FILE *std = fopen(stdoutput, "r");
    FILE *out = fopen(output, "r");

    //cout << stdoutput << endl << output << endl;
    //system("pause");

    /** 文件打开失败 */
    if(std == NULL || out == NULL)
    {
        cs.state = SYSTEM_ERROR;
        strcpy(cs.err_code, "No output file or no std output file.");
        return false;
    }

    char tmp1, tmp2;
    //if(stdsize == newsize)
    {
        bool r = true;
        while(!feof(std))
        {
            tmp1 = fgetc(std);
            tmp2 = fgetc(out);

            /** 去除LINUX和WINDOWS的换行符干扰 */
            while(tmp1 == '\r') tmp1 = fgetc(std);
            while(tmp2 == '\r') tmp2 = fgetc(out);

            if(tmp1 != tmp2)
            {
                r = false;
                break;
            }
        }

        /** 若是AC */
        if(r)
        {
            fclose(std);
            fclose(out);
            cs.state = ACCEPTED;
            return true;
        }
    }

    cs.state = WRONG_ANSWER;

    /** TODO: 判断PE */

    fclose(std);
    fclose(out);

    return false;
}

/**
 * @brief 评测函数
 * 通过对比输出文件进行评测
 *
 * @param exe 已编译好的可执行文件名
 * @param stdinput 标准输入文件名
 * @param stdoutput 标准输出文件名
 * @param cs 用于接收代码状态的引用
 * @return 若评测结果为ACCEPTED则返回true，其它一律返回false
 */
bool CNBUTOJCore::Judge(const char *exe, const char *stdinput, const char *stdoutput, const __int64 lim_time, const SIZE_T lim_memo, CodeState &cs, bool bPause)
{
    cs.exe_time = RUN_NO_TIME;
    cs.exe_memory = RUN_NO_MEMO;

    /** 几个文件路径 */
    string newexe = string(TEMP_PATH) + exe;
    string exeoutput = string(TEMP_PATH) + string(".output");

    /** 检测标准输出文件 */
    if(!FileExists(stdoutput))
    {
        cs.state = SYSTEM_ERROR;
        strcpy(cs.err_code, "Wrong std output file.");

        return false;
    }

    /** 最大文件大小 */
    struct _stat stdoutputinfo;
    _stat(stdoutput, &stdoutputinfo);
    DWORD maxSize = stdoutputinfo.st_size * 2;

    /** 运行代码 */
    PROCESS_INFORMATION ProcInfo;
    HANDLE hInput, hOutput;
    HANDLE hProcess = RunCode(newexe.c_str(), stdinput, exeoutput.c_str(), cs, hInput, hOutput, ProcInfo);
    ReleaseIOHandle(hInput, hOutput);
    
    /** 若代码运行不成功 */
    if(NULL == hProcess) 
    {
        if(bPause) system("pause");
        //ReleaseIOHandle(hInput, hOutput);
        ClearUp(newexe.c_str(), exeoutput.c_str(), ProcInfo);
        return false;
    }

    /** 监视若有异常 */
    if(!WatchCode(hProcess, lim_time, lim_memo, maxSize, exeoutput.c_str(), cs, ProcInfo)) 
    {
        if(bPause) system("pause");
        //ReleaseIOHandle(hInput, hOutput);
        ClearUp(newexe.c_str(), exeoutput.c_str(), ProcInfo);
        return false;
    }

    /** 释放输入输出文件句柄 */
    //ReleaseIOHandle(hInput, hOutput);

    /** 判断代码正误 */
    if(!_IsRight(stdoutput, exeoutput.c_str(), cs))
    {
        if(bPause) system("pause");
        //ReleaseIOHandle(hInput, hOutput);
        ClearUp(newexe.c_str(), exeoutput.c_str(), ProcInfo);
        return false;
    }

    ClearUp(newexe.c_str(), exeoutput.c_str(), ProcInfo);
    //cs.state = ACCEPTED;

    return true;
}

/**
 * @brief 获取当前路径
 *
 * @return 当前程序所在路径
 */
string CNBUTOJCore::GetCurrentDir(void)
{
    return m_szProgramPath;
}
