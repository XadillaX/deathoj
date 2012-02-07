#include "CJudgeSql.h"

CJudgeSql::CJudgeSql(void) :
    m_szLastError(""),
    m_Conn(false),
    m_szLastSql("")
{
    InitializeCriticalSection(&m_CS);
}

CJudgeSql::~CJudgeSql(void)
{
}

void CJudgeSql::SetConnectionInfo(string dbhost, string username, string password, string dbname, int port)
{
    m_szDBHost = dbhost;
    m_szUsername = username;
    m_szPassword = password;
    m_szDBName = dbname;
    m_nPort = port;
}

bool CJudgeSql::Connect()
{
    bool rst = m_Conn.connect(m_szDBName.c_str(), m_szDBHost.c_str(), m_szUsername.c_str(), m_szPassword.c_str(), m_nPort);
    if(!rst)
    {
        m_szLastError = m_Conn.error();
    }

    return rst;
}

void CJudgeSql::Disconnect()
{
    m_Conn.disconnect();
}

string CJudgeSql::GetLastError()
{
    ::EnterCriticalSection(&m_CS);
    string err = m_szLastError;
    ::LeaveCriticalSection(&m_CS);

    return err;
}

void CJudgeSql::SetPrefix(string prefix)
{
    m_szPrefix = prefix;
}

string CJudgeSql::GetPrefix()
{
    return m_szPrefix;
}

string CJudgeSql::GetFieldName(string field)
{
    return m_szPrefix + field;
}

string CJudgeSql::GetLastSql()
{
    ::EnterCriticalSection(&m_CS);
    string sql = m_szLastSql;
    ::LeaveCriticalSection(&m_CS);

    return sql;
}

tagSQL_JUDGE_INFO* CJudgeSql::GetNextQueuingRecord()
{
    string submit = GetFieldName("submit");
    string contestproblem = GetFieldName("contestproblem");
    string problem = GetFieldName("problem");
    string code = GetFieldName("code");

    /** SQL语句 */
    string sql = "SELECT " + contestproblem + ".contestid, " + contestproblem + ".index, " + problem + ".timelimit, " + problem + ".memorylimit, " + problem + ".inputmd5, " + problem + ".outputmd5, " + submit + ".totsubmitid, " + submit + ".submitid, " + problem + ".problemid, " + submit + ".userid, " + submit + ".languageid, " + code + ".code ";
    sql += ("FROM " + submit + ", " + code + ", " + contestproblem + ", " + problem);
    sql += (" WHERE " + submit + ".totsubmitid = " + code + ".totsubmitid AND " + submit + ".contestid = " + contestproblem + ".contestid ");
    sql += ("AND " + submit + ".index = " + contestproblem + ".index AND " + problem + ".problemid = " + contestproblem + ".problemid ");
    sql += ("AND " + submit + ".resultid = 0 ORDER BY " + submit + ".totsubmitid ASC LIMIT 0, 1");

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;

    /** 查询 */
    if(!Connect())
    {
        ::LeaveCriticalSection(&m_CS);
        return NULL;
    }
    mysqlpp::Query query = m_Conn.query(sql);
    if(mysqlpp::StoreQueryResult res = query.store())
    {
        if(0 == res.size())
        {
            m_szLastError = "没有符合条件的记录。";
            Disconnect();
            ::LeaveCriticalSection(&m_CS);
            return NULL;
        }

        tagSQL_JUDGE_INFO* info = new tagSQL_JUDGE_INFO();

        info->languageid = res[0]["languageid"];
        info->problemid = res[0]["problemid"];
        info->submitid = res[0]["submitid"];
        info->totsubmitid = res[0]["totsubmitid"];
        info->userid = res[0]["userid"];
        info->code = string(res[0]["code"]);
        info->lim_memo = res[0]["memorylimit"];
        info->lim_time = res[0]["timelimit"];
        info->contestid = res[0]["contestid"];
        info->problemindex = res[0]["index"];
        string md51 = res[0]["inputmd5"], md52 = res[0]["outputmd5"];
        strcpy(info->input_md5, md51.c_str());
        strcpy(info->output_md5, md52.c_str());

        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return info;
    }
    else
    {
        Disconnect();
        m_szLastError = query.error();
        ::LeaveCriticalSection(&m_CS);
        return NULL;
    }
    ::LeaveCriticalSection(&m_CS);

    Disconnect();
    return NULL;
}

bool CJudgeSql::TurnToCompiling(int totsubmitid)
{
    /** SQL语句 */
    string submit = GetFieldName("submit");
    string sql = "UPDATE " + submit + " SET resultid = 1 WHERE totsubmitid = " + XStringFunc::IntToString(totsubmitid);

    /** 互斥对象 */
    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;

    /** 连接数据库 */
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return false;
    }

    /** 开始查询 */
    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        mysqlpp::StoreQueryResult res = query.store();
    }
    catch(exception &e)
    {
        Disconnect();
        m_szLastError = e.what();
        ::LeaveCriticalSection(&m_CS);
        
        return false;
    }

    /** 断开数据库 */
    Disconnect();
    ::LeaveCriticalSection(&m_CS);

    return true;
}

string CJudgeSql::GetUserAcceptList(int userid)
{
    string user = GetFieldName("user");
    string sql = "SELECT solvedlist FROM " + user + " WHERE userid = " + XStringFunc::IntToString(userid);

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;

    /** 查询 */
    if(!Connect())
    {
        ::LeaveCriticalSection(&m_CS);
        return NULL;
    }
    mysqlpp::Query query = m_Conn.query(sql);
    if(mysqlpp::StoreQueryResult res = query.store())
    {
        if(0 == res.size())
        {
            m_szLastError = "没有符合条件的记录。";
            Disconnect();
            ::LeaveCriticalSection(&m_CS);
            return "";
        }

        //tagSQL_JUDGE_INFO* info = new tagSQL_JUDGE_INFO();

        string result = res[0]["solvedlist"];

        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return result;
    }
    else
    {
        Disconnect();
        m_szLastError = query.error();
        ::LeaveCriticalSection(&m_CS);
        return "";
    }
    ::LeaveCriticalSection(&m_CS);

    Disconnect();
    return "";
}

bool CJudgeSql::AddProblemAccept(int contestid, string index)
{
    string contestproblem = GetFieldName("contestproblem");
    string sql = "UPDATE " + contestproblem + " SET solved = solved + 1 WHERE contestid = " + XStringFunc::IntToString(contestid) + " AND `index` = '" + FilterSQLString(index) + "'";

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return false;
    }

    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        mysqlpp::StoreQueryResult res = query.store();
    }
    catch(exception &e)
    {
        Disconnect();
        m_szLastError = e.what();
        ::LeaveCriticalSection(&m_CS);
        
        return false;
    }

    Disconnect();
    ::LeaveCriticalSection(&m_CS);
    return true;
}

bool CJudgeSql::AddUserAccept(int userid, string index)
{
    /** SQL语句 */
    string user = GetFieldName("user");
    string sql = "UPDATE " + user + " SET solved = solved + 1 WHERE userid = " + XStringFunc::IntToString(userid);

    /** 互斥对象 */
    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;

    /** 连接数据库 */
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return false;
    }

    /** 开始查询 */
    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        mysqlpp::StoreQueryResult res = query.store();
    }
    catch(exception &e)
    {
        Disconnect();
        m_szLastError = e.what();
        ::LeaveCriticalSection(&m_CS);
        
        return false;
    }

    /** 是否要更新AC列表 */
    bool bExist = false;
    string szlist = GetUserAcceptList(userid);
    XStringFunc str_func(szlist);
    static string prob_list[65535];
    int count = str_func.Split("|", prob_list, 0, true);
    for(int i = 0; i < count; i++)
    {
        if(index == prob_list[i])
        {
            bExist = true;
            break;
        }
    }
    if(!bExist)
    {
        /** 更新用户AC */
        szlist += (index + "|");
        bool result = UpdateUserAcceptList(userid, szlist);
    }

    /** 断开数据库 */
    Disconnect();
    ::LeaveCriticalSection(&m_CS);

    return true;
}

bool CJudgeSql::UpdateUserAcceptList(int userid, string szlist)
{
    string user = GetFieldName("user");
    string sql = "UPDATE " + user + " SET solvedlist = '" + FilterSQLString(szlist) + "', solvednum = solvednum + 1 WHERE userid = " + XStringFunc::IntToString(userid);

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return false;
    }

    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        mysqlpp::StoreQueryResult res = query.store();
    }
    catch(exception &e)
    {
        Disconnect();
        m_szLastError = e.what();
        ::LeaveCriticalSection(&m_CS);
        
        return false;
    }

    Disconnect();
    ::LeaveCriticalSection(&m_CS);
    return true;
}

bool CJudgeSql::AddRuntimeError(int totsubmitid, string error)
{
    string submit = GetFieldName("runtimeerror");
    string sql = "INSERT INTO " + submit + "(totsubmitid, message) VALUES(" + XStringFunc::IntToString(totsubmitid) + ", '" + FilterSQLString(error) + "')";

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return false;
    }

    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        mysqlpp::StoreQueryResult res = query.store();
    }
    catch(exception &e)
    {
        Disconnect();
        m_szLastError = e.what();
        ::LeaveCriticalSection(&m_CS);
        
        return false;
    }

    Disconnect();
    ::LeaveCriticalSection(&m_CS);
    return true;
}

bool CJudgeSql::TurnResultStatus(int resultid, int time, int memory, string condition)
{
    string submit = GetFieldName("submit");
    string sql = "UPDATE " + submit + " SET resultid = " + XStringFunc::IntToString(resultid);
    sql += (", time = " + XStringFunc::IntToString(time) + ", memory = " + XStringFunc::IntToString(memory));
    if("" != condition)
    {
        sql += (" WHERE " + condition);
    }

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return false;
    }

    /** 开始查询 */
    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        mysqlpp::StoreQueryResult res = query.store();
    }
    catch(exception &e)
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        m_szLastError = e.what();
        return false;
    }

    /** 断开数据库 */
    Disconnect();
    ::LeaveCriticalSection(&m_CS);

    return true;
}

int CJudgeSql::GetSubmissionsByTime(int contestid, vector<tagSQL_RES_INFO>& res)
{
    res.clear();

    string submit = GetFieldName("submit");
    string sql = "SELECT `totsubmitid`, `submitid`, `contestid`, `index`, `userid`, `resultid`, `submittime` FROM `" +
        submit + "` WHERE `resultid` > 2 AND `resultid` != 14 AND `contestid` = " + XStringFunc::IntToString(contestid) + " ORDER BY `submittime` ASC";

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return 0;
    }

    /** 开始查询 */
    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        if(mysqlpp::StoreQueryResult Qres = query.store())
        {
            tagSQL_RES_INFO info;
            int cnt = Qres.size();
            for(int i = 0; i < cnt; i++)
            {
                info.contestid = Qres[i]["contestid"];
                info.problemindex = Qres[i]["index"];
                info.resultid = Qres[i]["resultid"];
                info.submitid = Qres[i]["submitid"];
                info.time = Qres[i]["submittime"];
                info.totsubmitid = Qres[i]["totsubmitid"];
                info.userid = Qres[i]["userid"];

                res.push_back(info);
            }

            Disconnect();
            ::LeaveCriticalSection(&m_CS);
            
            return cnt;
        }
        else
        {
            Disconnect();
            ::LeaveCriticalSection(&m_CS);
            m_szLastError = query.error();
            return 0;
        }
    }
    catch(exception &e)
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        m_szLastError = e.what();
        return 0;
    }
}

bool CJudgeSql::UpdateRankVersion(int contestid, string version)
{
    //string res = "00000000000000";
    //
    //time_t timer;
    //struct tm *tblock;
    //timer = time(NULL);
    //tblock = localtime(&timer);

    //char r[20];
    //sprintf(r, "%04d%02d%02d%02d%02d%02d", tblock->tm_year + 1900, tblock->tm_mon + 1, tblock->tm_mday,
    //    tblock->tm_hour, tblock->tm_min, tblock->tm_sec);

    //res = r;
    
    string contest = this->GetFieldName("contest");
    string sql = "UPDATE `" + contest + "` SET `resultversion` = '" + version + "' WHERE `contestid` = " + XStringFunc::IntToString(contestid);

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return false;
    }

    /** 开始查询 */
    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        mysqlpp::StoreQueryResult res = query.store();
    }
    catch(exception &e)
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        m_szLastError = e.what();
        return false;
    }

    /** 断开数据库 */
    Disconnect();
    ::LeaveCriticalSection(&m_CS);

    return true;
}

int CJudgeSql::GetContestStartTime(int contestid)
{
    string contest = this->GetFieldName("contest");

    string sql = "SELECT `starttime` FROM `" + contest + "` WHERE `contestid` = " + XStringFunc::IntToString(contestid);

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return 0;
    }

    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        if(mysqlpp::StoreQueryResult res = query.store())
        {
            if(0 == res.size())
            {
                m_szLastError = "没有符合条件的记录。";
                Disconnect();
                ::LeaveCriticalSection(&m_CS);
                return 0;
            }

            int result = res[0]["starttime"];

            Disconnect();
            ::LeaveCriticalSection(&m_CS);
            return result;
        }
        else
        {
            Disconnect();
            m_szLastError = query.error();
            ::LeaveCriticalSection(&m_CS);
            return 0;
        }
    }
    catch(exception &e)
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        m_szLastError = e.what();
        return 0;
    }
}

string CJudgeSql::GetRankVersion(int contestid)
{
    string contest = this->GetFieldName("contest");

    string sql = "SELECT `resultversion` FROM `" + contest + "` WHERE `contestid` = " + XStringFunc::IntToString(contestid);

    ::EnterCriticalSection(&m_CS);
    m_szLastSql = sql;
    if(!Connect())
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        return 0;
    }

    try
    {
        mysqlpp::Query query = m_Conn.query(sql);
        if(mysqlpp::StoreQueryResult res = query.store())
        {
            if(0 == res.size())
            {
                m_szLastError = "没有符合条件的记录。";
                Disconnect();
                ::LeaveCriticalSection(&m_CS);
                return "";
            }

            //tagSQL_JUDGE_INFO* info = new tagSQL_JUDGE_INFO();

            string result = res[0]["resultversion"];

            Disconnect();
            ::LeaveCriticalSection(&m_CS);
            return result;
        }
        else
        {
            Disconnect();
            m_szLastError = query.error();
            ::LeaveCriticalSection(&m_CS);
            return "";
        }
    }
    catch(exception &e)
    {
        Disconnect();
        ::LeaveCriticalSection(&m_CS);
        m_szLastError = e.what();
        return 0;
    }
}

string CJudgeSql::FilterSQLString(string str)
{
    /** 找\\ */
    int pos = 0;
    string before, after;
    while((pos = str.find("\\", pos + 2)) != string::npos)
    {
        before = str.substr(0, pos);
        after = str.substr(pos + 1);
        str = before + "\\\\" + after;
    }

    /** 找单引号 */
    pos = 0;
    while((pos = str.find("'", pos + 2)) != string::npos)
    {
        before = str.substr(0, pos);
        after = str.substr(pos + 1);
        str = before + "\\'" + after;
    }

    return str;
}
