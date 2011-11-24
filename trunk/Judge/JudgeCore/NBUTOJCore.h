//////////////////////////////////////////////////////////////////////////  
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 ������ͼ(http://www.x2studio.net)
/// All rights reserved.
///
/// @file NBUTOJCore.h
/// @brief ���������ͷ�ļ�
///
/// ������dll�ĺ�����CNBUTOJCore�Լ�����һЩ����
///  
/// @version 1.0
/// @author �쿭��
/// @date 2010-12-7
///  
///  
/// �޶�˵��������汾
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

#define RUN_NO_TIME              0                              ///< ����֮����ʱ��
#define RUN_NO_MEMO              0                              ///< ����֮�����ڴ�
#define COMPILERS_PATH          "compilers\\"                   ///< ������·��
#define TEMP_PATH               "tmpdir\\"                      ///< ��ʱĿ¼

/**
 * @brief ����״̬ö��
 * ���д���״̬������ǰ̨��ʾ
 */
enum NState {
    QUEUING,                        ///< ���������
    COMPILING,                      ///< ������
    RUNNING,                        ///< ������
    ACCEPTED,                       ///< ͨ��
    PRESENTATION_ERROR,             ///< ��ʽ����
    WRONG_ANSWER,                   ///< �𰸴���
    RUNTIME_ERROR,                  ///< ����ʱ����
    TIME_LIMIT_EXCEEDED_1,          ///< ���г�ʱ
    TIME_LIMIT_EXCEEDED_2,          ///< ���г�ʱ
    MEMORY_LIMIT_EXCEEDED,          ///< �ڴ泬��
    OUTPUT_LIMIT_EXCEEDED,          ///< �������
    COMPILATION_ERROR,              ///< �������
    COMPILATION_SUC,                ///< ����ͨ��
    SYSTEM_ERROR,                   ///< ϵͳ����
    OUT_OF_CONTEST_TIME             ///< ��������ʱ��
};


/**
 * @brief ����״̬�ṹ��
 * ����������״̬���ڴ�ռ�á�ʱ��ռ�á�������Ϣ�ȡ�
 */
struct CodeState {
    NState state;                   ///< ״̬
    char err_code[10240];           ///< ������Ϣ
    __int64 exe_time;               ///< ����ʱ��
    SIZE_T exe_memory;              ///< �����ڴ�

    CodeState() 
    {
        err_code[0] = '\0';
    };
};

/**
 * @brief �����ں���
 * NBUTOJ������������ں�dll�ĺ�����
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
