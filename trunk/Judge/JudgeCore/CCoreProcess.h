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
using namespace std;

class CCoreProcess : public Singleton<CCoreProcess>
{
public:
    CCoreProcess(void);
    virtual ~CCoreProcess(void);

    void                        EnterMainLoop();

private:
    bool                        UpdateState(tagSQL_JUDGE_INFO* pSJI, CodeState* code_state);

private:
    string                      m_szDataPath;

    //CNBUTOJCore                 m_Judger;
};
#endif
