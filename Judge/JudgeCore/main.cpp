#include <iostream>
#include <string>
#include "XStringFunc.h"
#include "CCFGReader.h"
#include "CJudgeSql.h"
#include "CMyLogger.h"
#include "CCoreProcess.h"
using namespace std;

bool InitDatabase()
{
    char msg[512];
    CMyLogger::Instance().SetLog("初始化数据库...");

    /** 读取配置信息 */
    string dbhost, dbname, username, password, prefix;
    int port;
    CCFGReader reader("config.cfg");
    dbhost = reader.GetString("DBHOST");
    dbname = reader.GetString("DBNAME");
    username = reader.GetString("USERNAME");
    password = reader.GetString("PASSWORD");
    prefix = reader.GetString("DBPREFIX");
    port = reader.GetInt("DBPORT");
    sprintf(
        msg,
        "数据库主机 - %s:%d; 数据库名 - %s; 用户名 - %s; 密码 - %s; 表前缀 - %s;",
        dbhost.c_str(),
        port,
        dbname.c_str(),
        username.c_str(),
        password.c_str(),
        prefix.c_str()
        );
    CMyLogger::Instance().SetLog("读取数据库配置信息", msg, true, false);

    /** 配置数据库相关类 */
    CJudgeSql::Instance().SetConnectionInfo(dbhost, username, password, dbname, port);
    CJudgeSql::Instance().SetPrefix(prefix);

    /** 测试连接数据库 */
    if(CJudgeSql::Instance().TestConnect())
    {
        CMyLogger::Instance().SetLog("测试连接数据库", "成功", true, false, "#000", "green");
        return true;
    }
    else
    {
        CMyLogger::Instance().SetLog("测试连接数据库", "失败", true, false, "#000", "red");
        return false;
    }

    return true;
}

int main()
{
    CMyLogger::Instance().SetLog("评测端内核启动中...");
    if(!InitDatabase())
    {
        /** 数据库初始化出错 */
        return 0;
    }

    /** 启动服务 */
    CCoreProcess::Instance().EnterMainLoop();

    return 0;
}
