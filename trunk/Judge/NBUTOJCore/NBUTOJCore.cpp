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

/** ���ຯ�� */
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

/** ���Ա���� */
CNBUTOJCore::CNBUTOJCore()
{
    char dir[512];
    GetCurrentDirectory(512 * sizeof(char), dir);
    m_szProgramPath = string(dir);
	
    return;
}

CNBUTOJCore::~CNBUTOJCore()
{
    /** Todo: �������� */
}

/**
 * @brief ���뺯��
 * �������ļ����ݶ�Ӧ����������ɿ�ִ���ļ�
 * 
 * @param type ����������
 * @param input �����ļ���
 * @param output ��ִ���ļ���
 * @param cs ���ڽ��մ���״̬������
 * @return ������ɹ�����true�����򷵻�false
 */
bool CNBUTOJCore::CompileFile(const string type, const string input, const string output, CodeState &cs)
{
    /** ���ݱ��������ʹ���������� */
    NCompiler *cp = CPFactories::Instance().Create(type);
    
    /** ���Ҳ�����Ӧ������ */
    if(!cp)
    {
        cs.state = SYSTEM_ERROR;
        strcpy(cs.err_code, "Wrong Compiler.");
        return false;
    }

    /** ��ȡ��·�� */
    string newinput = string(TEMP_PATH) + input;
    string newoutput = string(TEMP_PATH) + output;

    /** �����ļ� */
    bool r = cp->CompileFile(newinput.c_str(), newoutput.c_str(), cs.err_code);
    
    /** ����״̬ */
    if(r) 
    {
        cs.state = COMPILATION_SUC;
        strcpy(cs.err_code, "\0");
    }
    else cs.state = COMPILATION_ERROR;

    /** ɾ������������ָ�� */
    SAFEDEL(cp);

    return r;
}

/**
 * @brief ���н��̺���
 * ����������Ĵ���Ŀ�ִ���ļ�����
 *
 * @param exe ��ִ���ļ��ļ���
 * @param input ���������ļ���
 * @param output ��������ļ���
 * @param cs ���ڽ��մ���״̬������
 * @param hInput ���ڽ��������ļ����
 * @param hOutput ���ڽ�������ļ����
 * @return �����̴����ɹ��򷵻ؽ��̾�������򷵻�NULL
 */
HANDLE CNBUTOJCore::RunCode(const char *exe, const char *input, const char *output, CodeState &cs, HANDLE &hInput, HANDLE &hOutput, PROCESS_INFORMATION &inProcInfo)
{
    SECURITY_ATTRIBUTES sa;
    sa.bInheritHandle = true;
    sa.nLength = sizeof(sa);
    sa.lpSecurityDescriptor = NULL;

    /** �򿪵õ���������ļ���� */
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

    /** �����������ļ����� */
    bool flag = CreateProcessA(exe, NULL, NULL,
        NULL, true, DEBUG_ONLY_THIS_PROCESS, NULL, NULL, &StartInfo, &ProcInfo);

    /** �����в��ɹ� */
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
 * @brief ���м��Ӻ���
 * ���ӽ�������״̬��ʱ�䡢�ڴ��Լ��������Ϣ
 *
 * @param hProcess ���̾��
 * @param lim_time ʱ������
 * @param lim_memo �ڴ�����
 * @param lim_size ����ļ���������
 * @param cs ���ڽ��մ���״̬������
 * @return �������ڼ�δ�����쳣�򷵻�true�����򷵻�false
 */
bool CNBUTOJCore::WatchCode(const HANDLE hProcess, const __int64 lim_time, const SIZE_T lim_memo,
                            const DWORD lim_size, const char *outputFilename, CodeState &cs, const PROCESS_INFORMATION ProcInfo)
{
    DWORD                       maxMemo = 0;                    ///< ʹ�õ��ڴ�
    __int64                     RunTime = -1;                   ///< ʹ�õ�ʱ��
    DWORD                       nowSize;                        ///< ʹ�õ������С
    DEBUG_EVENT                 DBE;                            ///< ������Ϣ�ṹ��

    while(true)
    {
        /** �������ԣ�ʱ����Ϊ�˺��Զϵ�ģ� */
        bool flag = WaitForDebugEvent(&DBE, lim_time + 200);
        if(!flag)
        {
            RunTime = _GetRunTime(hProcess) + lim_time + 200;
            return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, TIME_LIMIT_EXCEEDED_2, RunTime, maxMemo);
        }

        /** ��ȡ����ʱ�� */
        RunTime = _GetRunTime(hProcess);
        if(-1 == RunTime)
            return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, SYSTEM_ERROR, RunTime, maxMemo, "Can't get running time.");

        /** ��ȡ�����ڴ� */
        DWORD memo = _GetRunMemo(hProcess);
        if(-1 == memo) return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, SYSTEM_ERROR, RunTime, maxMemo, "Can't get occupied memory.");
        else maxMemo = (memo > maxMemo) ? memo : maxMemo;

        /** ��ȡ�����ļ������С */
        nowSize = _GetRunSize(outputFilename);

        /** ����ʱ */
        if(RunTime > lim_time)
            return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, TIME_LIMIT_EXCEEDED_1, RunTime, maxMemo);
        
        //cout << RunTime << " " << maxMemo << " " << nowSize << " " << DBE.dwDebugEventCode << endl;

        /** �����ڴ� */
        if(maxMemo > lim_memo)
            return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, MEMORY_LIMIT_EXCEEDED, RunTime, maxMemo);

        /** ������� */
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
                /** ���ƴ��� */
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

                /** �رվ����������Խ��̲��ܱ����� */
                CloseHandle(DBE.u.CreateProcessInfo.hFile);
                CloseHandle(DBE.u.CreateProcessInfo.hProcess);
                CloseHandle(DBE.u.CreateProcessInfo.hThread);
                return _ErrExit(hProcess, ProcInfo.dwProcessId, cs, RUNTIME_ERROR, RunTime, maxMemo);
            }
        }

        /** �������� */
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
 * @brief �ͷ�IO�������
 * �ͷ�ԭ�ȴ򿪵�IO���
 *
 * @param hInput �����ļ����
 * @param hOutput ����ļ����
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

    /** �ļ���ʧ�� */
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

            /** ȥ��LINUX��WINDOWS�Ļ��з����� */
            while(tmp1 == '\r') tmp1 = fgetc(std);
            while(tmp2 == '\r') tmp2 = fgetc(out);

            if(tmp1 != tmp2)
            {
                r = false;
                break;
            }
        }

        /** ����AC */
        if(r)
        {
            fclose(std);
            fclose(out);
            cs.state = ACCEPTED;
            return true;
        }
    }

    cs.state = WRONG_ANSWER;

    /** TODO: �ж�PE */

    fclose(std);
    fclose(out);

    return false;
}

/**
 * @brief ���⺯��
 * ͨ���Ա�����ļ���������
 *
 * @param exe �ѱ���õĿ�ִ���ļ���
 * @param stdinput ��׼�����ļ���
 * @param stdoutput ��׼����ļ���
 * @param cs ���ڽ��մ���״̬������
 * @return ��������ΪACCEPTED�򷵻�true������һ�ɷ���false
 */
bool CNBUTOJCore::Judge(const char *exe, const char *stdinput, const char *stdoutput, const __int64 lim_time, const SIZE_T lim_memo, CodeState &cs, bool bPause)
{
    cs.exe_time = RUN_NO_TIME;
    cs.exe_memory = RUN_NO_MEMO;

    /** �����ļ�·�� */
    string newexe = string(TEMP_PATH) + exe;
    string exeoutput = string(TEMP_PATH) + string(".output");

    /** ����׼����ļ� */
    if(!FileExists(stdoutput))
    {
        cs.state = SYSTEM_ERROR;
        strcpy(cs.err_code, "Wrong std output file.");

        return false;
    }

    /** ����ļ���С */
    struct _stat stdoutputinfo;
    _stat(stdoutput, &stdoutputinfo);
    DWORD maxSize = stdoutputinfo.st_size * 2;

    /** ���д��� */
    PROCESS_INFORMATION ProcInfo;
    HANDLE hInput, hOutput;
    HANDLE hProcess = RunCode(newexe.c_str(), stdinput, exeoutput.c_str(), cs, hInput, hOutput, ProcInfo);
    ReleaseIOHandle(hInput, hOutput);
    
    /** ���������в��ɹ� */
    if(NULL == hProcess) 
    {
        if(bPause) system("pause");
        //ReleaseIOHandle(hInput, hOutput);
        ClearUp(newexe.c_str(), exeoutput.c_str(), ProcInfo);
        return false;
    }

    /** ���������쳣 */
    if(!WatchCode(hProcess, lim_time, lim_memo, maxSize, exeoutput.c_str(), cs, ProcInfo)) 
    {
        if(bPause) system("pause");
        //ReleaseIOHandle(hInput, hOutput);
        ClearUp(newexe.c_str(), exeoutput.c_str(), ProcInfo);
        return false;
    }

    /** �ͷ���������ļ���� */
    //ReleaseIOHandle(hInput, hOutput);

    /** �жϴ������� */
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
 * @brief ��ȡ��ǰ·��
 *
 * @return ��ǰ��������·��
 */
string CNBUTOJCore::GetCurrentDir(void)
{
    return m_szProgramPath;
}
