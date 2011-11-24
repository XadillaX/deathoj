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
    CMyLogger::Instance().SetLog("��ʼ�����ݿ�...");

    /** ��ȡ������Ϣ */
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
        "���ݿ����� - %s:%d; ���ݿ��� - %s; �û��� - %s; ���� - %s; ��ǰ׺ - %s;",
        dbhost.c_str(),
        port,
        dbname.c_str(),
        username.c_str(),
        password.c_str(),
        prefix.c_str()
        );
    CMyLogger::Instance().SetLog("��ȡ���ݿ�������Ϣ", msg, true, false);

    /** �������ݿ������ */
    CJudgeSql::Instance().SetConnectionInfo(dbhost, username, password, dbname, port);
    CJudgeSql::Instance().SetPrefix(prefix);

    /** �����������ݿ� */
    if(CJudgeSql::Instance().TestConnect())
    {
        CMyLogger::Instance().SetLog("�����������ݿ�", "�ɹ�", true, false, "#000", "green");
        return true;
    }
    else
    {
        CMyLogger::Instance().SetLog("�����������ݿ�", "ʧ��", true, false, "#000", "red");
        return false;
    }

    return true;
}

int main()
{
    CMyLogger::Instance().SetLog("������ں�������...");
    if(!InitDatabase())
    {
        /** ���ݿ��ʼ������ */
        return 0;
    }

    /** �������� */
    CCoreProcess::Instance().EnterMainLoop();

    return 0;
}
