#ifndef CJUDGESQL_H
#define CJUDGESQL_H
#pragma once
#include "Singleton.h"
#include <mysql++.h>
#include <string>
#include "XStringFunc.h"
#include <vector>
using namespace std;

struct tagSQL_JUDGE_INFO
{
    int                                 totsubmitid;
    int                                 submitid;
    int                                 problemid;
    int                                 contestid;
    string                              problemindex;
    int                                 userid;
    int                                 languageid;

    int                                 lim_time;
    int                                 lim_memo;

    char                                input_md5[33];              ///< 用于以后分布式
    char                                output_md5[33];             ///< 用于以后分布式

    string                              code;
};

struct tagSQL_RES_INFO
{
    int                                 totsubmitid;
    int                                 submitid;
    int                                 contestid;
    string                              problemindex;

    int                                 userid;
    int                                 resultid;

    int                                 time;
};

class CJudgeSql : public Singleton<CJudgeSql>
{
public:
    CJudgeSql(void);
    virtual ~CJudgeSql(void);

    void                                SetConnectionInfo(string dbhost, string username, string password, string dbname, int port);

    bool                                Connect();
    void                                Disconnect();

    bool                                TestConnect()
    {
        ::EnterCriticalSection(&m_CS);
        bool result = Connect();
        Disconnect();
        ::LeaveCriticalSection(&m_CS);

        return result;
    }

    void                                SetPrefix(string prefix);
    string                              GetPrefix();
    string                              GetFieldName(string field);

    string                              GetLastError();     ///< 获取最后的错误
    string                              GetLastSql();       ///< 获取最后的SQL

    string                              FilterSQLString(string str);

public:
    tagSQL_JUDGE_INFO*                  GetNextQueuingRecord();
    bool                                TurnToCompiling(int totsubmitid);
    bool                                TurnResultStatus(int resultid, int time = 0, int memory = 0, string condition = "");
    bool                                AddRuntimeError(int totsubmitid, string error);

    string                              GetUserAcceptList(int userid);
    bool                                AddUserAccept(int userid, string index);
    bool                                UpdateUserAcceptList(int userid, string szlist);
    bool                                AddProblemAccept(int contestid, string index);

    int                                 GetSubmissionsByTime(int contestid, vector<tagSQL_RES_INFO>& res);
    string                              GetRankVersion(int contestid);
    bool                                UpdateRankVersion(int contestid, string version);

    int                                 GetContestStartTime(int contestid);

private:
    mysqlpp::Connection                 m_Conn;             ///< 连接对象
    string                              m_szLastError;      ///< 最后的错误
    string                              m_szLastSql;        ///< 最后的SQL
    string                              m_szPrefix;         ///< 前缀

    CRITICAL_SECTION                    m_CS;               ///< 互斥量

    string                              m_szDBHost;
    string                              m_szUsername;
    string                              m_szPassword;
    string                              m_szDBName;
    int                                 m_nPort;
};

#endif
