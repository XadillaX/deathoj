#ifndef COREPROCESS_H
#define COREPROCESS_H
#pragma once
#include "Singleton.h"
#include <iostream>
#include <string>
#include "XStringFunc.h"
#include "CCFGReader.h"
#include "CJudgeSql.h"
#include "CMyLogger.h"
#include "NBUTOJCore.h"
#include <algorithm>
using namespace std;

struct tagRANK_MAP_PROB_ELEMENT
{
    string                      problemindex;
    int                         time;
    int                         fine;

    bool                        ac;

    tagRANK_MAP_PROB_ELEMENT()
    {
        time = fine = 0, ac = false;
    }
};

struct tagRANK_MAP_ELEMENT
{
    int                         userid;
    map<string, tagRANK_MAP_PROB_ELEMENT> RMPE;

    int                         acnum;
    int                         time;

    tagRANK_MAP_ELEMENT()
    {
        userid = time = acnum = 0;
    }
};

class CCoreProcess : public Singleton<CCoreProcess>
{
public:
    CCoreProcess(void);
    virtual ~CCoreProcess(void);

    void                        EnterMainLoop();

private:
    bool                        UpdateState(tagSQL_JUDGE_INFO* pSJI, CodeState* code_state);
    void                        UpdateRank(int nContestID);

    string                      GetRankFilename(int contestid, string version);

private:
    string                      m_szDataPath;
    string                      m_szRankPath;

    //CNBUTOJCore                 m_Judger;
    vector<tagSQL_RES_INFO>     m_RankArray;
};
#endif
