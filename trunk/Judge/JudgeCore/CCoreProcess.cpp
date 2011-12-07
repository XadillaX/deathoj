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
            "数据库写入错误",
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
        sprintf(tmp, "用户id: %d; 题目编号: %d; 状态: %s.", pSJI->userid, pSJI->problemid, StateList[code_state->state].c_str());
        CMyLogger::Instance().SetLog("更新提交数据", tmp);

        /** 若有错误信息 */
        if(strlen(code_state->err_code) != 0)
        {
            if(!CJudgeSql::Instance().AddRuntimeError(pSJI->totsubmitid, code_state->err_code))
            {
#ifdef _DEBUG
                CMyLogger::Instance().SetLog("数据库错误", CJudgeSql::Instance().GetLastError() + "[" + CJudgeSql::Instance().GetLastSql() + "]", true, true, "#ccc");
#endif
            }
        }

        /** 若是AC */
        if(code_state->state == ACCEPTED)
        {
            /** 练习题库，更新用户AC数量 */
            if(pSJI->contestid == 1)
            {
                CJudgeSql::Instance().AddUserAccept(pSJI->userid, pSJI->problemindex);
            }

            /** 更新题目AC数量 */
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
    /** 进入主循环 */
    while(true)
    {
        /** 读取一条记录 */
        tagSQL_JUDGE_INFO* pSJI = CJudgeSql::Instance().GetNextQueuingRecord();

        /** 还没有带评测记录 */
        if(NULL == pSJI)
        {
            Sleep(500);
            continue;
        }

        /** 将其转换为COMPILING */
        CJudgeSql::Instance().TurnToCompiling(pSJI->totsubmitid);

        /** 输出代码文件名 */
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

        /** 写入代码 */
        FILE* fp = fopen(tmp_code_filename.c_str(), "w+");
        if(NULL == fp)
        {
            delete pSJI;
            Sleep(500);
            continue;
        }
        fprintf(fp, "%s", pSJI->code.c_str());
        fclose(fp);

        /** 输入输出数据 */
        CodeState code_state;
        string ipt_data = m_szDataPath + XStringFunc::IntToString(pSJI->problemid) + "\\data.in";
        string opt_data = m_szDataPath + XStringFunc::IntToString(pSJI->problemid) + "\\data.out";

        /** 将输入输出数据复制过来 */
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


        /** 编译代码 */
        CNBUTOJCore* judger = new CNBUTOJCore();
        
        memset(&code_state, 0, sizeof(CodeState));
        bool compile_result = judger->CompileFile(compiler, tmp_code_filename_ex, string(filename_timeid + ".exe").c_str(), code_state);
        
        /** 编译错误 */
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

        /** 更新为RUNNING */
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

        /** 等待评测完成 */
        pJP->WaitForJudgeFinished();

        /** 更新结果 */
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
