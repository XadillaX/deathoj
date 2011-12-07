#include "CCoreProcess.h"
#include <windows.h>

string StateList[] = {
    "QUEUING",
    "COMPILING",
    "RUNNING",
    "ACCEPTED",
    "PRESENTATION_ERROR",
    "WRONG_ANSWER",
    "RUNTIME_ERROR",
    "TIME_LIMIT_EXCEEDED_1",
    "TIME_LIMIT_EXCEEDED_2",
    "MEMORY_LIMIT_EXCEEDED",
    "OUTPUT_LIMIT_EXCEEDED",
    "COMPILATION_ERROR",
    "COMPILATION_SUC",
    "SYSTEM_ERROR",
    "OUT_OF_CONTEST_TIME",
    "DANGEROUS_CODE"
};

CCoreProcess::CCoreProcess(void) :
    m_szDataPath("D:\\")
{
    FILE* fp = fopen("datapath.cfg", "r");
    if(NULL == fp)
    {
        return;
    }
    char path[512];
    fgets(path, 511, fp);
    fclose(fp);

    m_szDataPath = path;
}

CCoreProcess::~CCoreProcess(void)
{
}

bool CCoreProcess::UpdateState(tagSQL_JUDGE_INFO* pSJI, CodeState* code_state)
{
    static char tmp[512];
    if(!CJudgeSql::Instance().TurnResultStatus(code_state->state, code_state->exe_time, code_state->exe_memory, "totsubmitid = " + XStringFunc::IntToString(pSJI->totsubmitid)))
    {
        CMyLogger::Instance().SetLog(
            "���ݿ�д�����",
            CJudgeSql::Instance().GetLastError()
            + "[" + CJudgeSql::Instance().GetLastSql()
            + "]",
            false,
            false,
            "red",
            "red"
            );
        return false;
    }
    else
    {
        sprintf(tmp, "�û�id: %d; ��Ŀ���: %d; ״̬: %s.", pSJI->userid, pSJI->problemid, StateList[code_state->state].c_str());
        CMyLogger::Instance().SetLog("�����ύ����", tmp);

        /** ���д�����Ϣ */
        if(strlen(code_state->err_code) != 0)
        {
            if(!CJudgeSql::Instance().AddRuntimeError(pSJI->totsubmitid, code_state->err_code))
            {
#ifdef _DEBUG
                CMyLogger::Instance().SetLog("���ݿ����", CJudgeSql::Instance().GetLastError() + "[" + CJudgeSql::Instance().GetLastSql() + "]", true, true, "#ccc");
#endif
            }
        }

        /** ����AC */
        if(code_state->state == ACCEPTED)
        {
            /** ��ϰ��⣬�����û�AC���� */
            if(pSJI->contestid == 1)
            {
                CJudgeSql::Instance().AddUserAccept(pSJI->userid, pSJI->problemindex);
            }

            /** ������ĿAC���� */
            CJudgeSql::Instance().AddProblemAccept(pSJI->contestid, pSJI->problemindex);
        }
    }

    return true;
}

struct judge_param
{
    string exe;
    string ipt;
    string opt;
    int time;
    int memo;
    CodeState* code_state;
    ::CRITICAL_SECTION cs;
    CNBUTOJCore* judger;

    bool bJudging;

    judge_param()
    {
        bJudging = true;
        ::InitializeCriticalSection(&cs);
    }

    void WaitForJudgeFinished()
    {
        while(true)
        {
            ::EnterCriticalSection(&cs);
            if(!bJudging)
            {
                ::LeaveCriticalSection(&cs);
                return;
            }
            ::LeaveCriticalSection(&cs);

            Sleep(1);
        }
    }
};

DWORD WINAPI JudgeThread(LPVOID lpParam)
{
    judge_param* pJP = (judge_param*)lpParam;
    bool result = pJP->judger->Judge(pJP->exe.c_str(), pJP->ipt.c_str(), pJP->opt.c_str(), pJP->time, pJP->memo, *(pJP->code_state));

    ::EnterCriticalSection(&(pJP->cs));
    pJP->bJudging = false;
    ::LeaveCriticalSection(&(pJP->cs));

    return 0;
}

void CCoreProcess::EnterMainLoop()
{
    /** ������ѭ�� */
    while(true)
    {
        /** ��ȡһ����¼ */
        tagSQL_JUDGE_INFO* pSJI = CJudgeSql::Instance().GetNextQueuingRecord();

        /** ��û�д������¼ */
        if(NULL == pSJI)
        {
            Sleep(500);
            continue;
        }

        /** ����ת��ΪCOMPILING */
        CJudgeSql::Instance().TurnToCompiling(pSJI->totsubmitid);

        /** ��������ļ��� */
        string filename_timeid = XStringFunc::IntToString(time(NULL));
        string tmp_code_filename = string(TEMP_PATH) + filename_timeid;
        string tmp_code_filename_ex = filename_timeid;
        string compiler;
        switch(pSJI->languageid)
        {
        case 1: tmp_code_filename += ".c"; tmp_code_filename_ex += ".c"; compiler = "GCC"; break;
        case 2: tmp_code_filename += ".cpp"; tmp_code_filename_ex += ".cpp"; compiler = "G++"; break;
        default: tmp_code_filename += ".cpp"; tmp_code_filename_ex += ".cpp"; compiler = "G++"; break;
        }

        /** д����� */
        FILE* fp = fopen(tmp_code_filename.c_str(), "w+");
        if(NULL == fp)
        {
            delete pSJI;
            Sleep(500);
            continue;
        }
        fprintf(fp, "%s", pSJI->code.c_str());
        fclose(fp);

        /** ����������� */
        CodeState code_state;
        string ipt_data = m_szDataPath + XStringFunc::IntToString(pSJI->problemid) + "\\data.in";
        string opt_data = m_szDataPath + XStringFunc::IntToString(pSJI->problemid) + "\\data.out";

        /** ������������ݸ��ƹ��� */
        /** $Id$ */
        FILE* ofp = NULL;
        char data_ch;
        fp = fopen(ipt_data.c_str(), "r");
        if(NULL == fp)
        {
            code_state.state = SYSTEM_ERROR;
            strcpy(code_state.err_code, "Can't open the std input file.");
            UpdateState(pSJI, &code_state);
            delete pSJI;
            Sleep(500);
            continue;
        }
        ofp = fopen((string(TEMP_PATH) + string("data.in")).c_str(), "w+");
        if(NULL == fp)
        {
            code_state.state = SYSTEM_ERROR;
            strcpy(code_state.err_code, "Can't create the std input file.");
            UpdateState(pSJI, &code_state);
            delete pSJI;
            Sleep(500);
            continue;
        }
        while((data_ch = fgetc(fp)) != EOF)
        {
            fprintf(ofp, "%c", data_ch);
        }
        fclose(fp);
        fclose(ofp);
        /////////////////////////////////////////////////
        fp = fopen(opt_data.c_str(), "r");
        if(NULL == fp)
        {
            remove(tmp_code_filename.c_str());
            code_state.state = SYSTEM_ERROR;
            strcpy(code_state.err_code, "Can't open the std output file.");
            UpdateState(pSJI, &code_state);
            delete pSJI;
            Sleep(500);
            continue;
        }
        ofp = fopen((string(TEMP_PATH) + string("data.out")).c_str(), "w+");
        if(NULL == fp)
        {
            remove(tmp_code_filename.c_str());
            code_state.state = SYSTEM_ERROR;
            strcpy(code_state.err_code, "Can't create the std output file.");
            UpdateState(pSJI, &code_state);
            delete pSJI;
            Sleep(500);
            continue;
        }
        while((data_ch = fgetc(fp)) != EOF)
        {
            fprintf(ofp, "%c", data_ch);
        }
        fclose(fp);
        fclose(ofp);


        /** ������� */
        CNBUTOJCore* judger = new CNBUTOJCore();
        
        memset(&code_state, 0, sizeof(CodeState));
        bool compile_result = judger->CompileFile(compiler, tmp_code_filename_ex, string(filename_timeid + ".exe").c_str(), code_state);
        
        /** ������� */
        if(!compile_result)
        {
            remove(tmp_code_filename.c_str());
            remove((string(TEMP_PATH) + string("data.in")).c_str());
            remove((string(TEMP_PATH) + string("data.out")).c_str());
            delete(judger);
            UpdateState(pSJI, &code_state);
            delete pSJI;
            Sleep(500);
            continue;
        }

        /** ����ΪRUNNING */
        code_state.err_code[0] = '\0';
        code_state.state = RUNNING;
        UpdateState(pSJI, &code_state);

        //bool judge_result = m_Judger.Judge("temp.exe", ipt_data.c_str(), opt_data.c_str(), pSJI->lim_time, pSJI->lim_memo, code_state);
        judge_param* pJP = new judge_param();
        pJP->code_state = &code_state;
        pJP->exe = string(filename_timeid + ".exe").c_str();
        pJP->ipt = string(TEMP_PATH) + string("data.in");//ipt_data;
        pJP->opt = string(TEMP_PATH) + string("data.out");//opt_data;
        pJP->memo = pSJI->lim_memo;
        pJP->time = pSJI->lim_time;
        pJP->judger = judger;
        ::CreateThread(0, 0, JudgeThread, (void*)pJP, 0, NULL);

        /** �ȴ�������� */
        pJP->WaitForJudgeFinished();

        /** ���½�� */
        UpdateState(pSJI, &code_state);

        remove(tmp_code_filename.c_str());
        remove(pJP->ipt.c_str());
        remove(pJP->opt.c_str());
        delete judger;
        delete pSJI;
        delete pJP;
        Sleep(500);
    }
}
