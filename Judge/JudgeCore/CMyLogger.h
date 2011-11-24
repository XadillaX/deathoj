#ifndef CMYLOGGER_H
#define CMYLOGGER_H
#pragma once
#include "Singleton.h"
#include <string>
using namespace std;

#define LOG_PATH            "LOG\\"

class CMyLogger : public Singleton<CMyLogger>
{
public:
    CMyLogger(void);
    virtual ~CMyLogger(void);

    void                    SetLog(string title, string body = "", bool titlebold = false, bool bodybold = false, string titlecolor = "#000", string bodycolor = "#000");

    void                    SetMaxLine(int line);                       ///< ������ʷ�ļ��������
    string                  GetFilename();                              ///< ��ȡ��ǰ��ʷ�ļ���
    bool                    CreateNextFile();                           ///< ������һ����ʷ�ļ�
    string                  CreateFilename();                           ///< ����һ����ʷ�ļ���
    string                  GetCurTime(char spliter1 = '-', char spliter2 = ':');

private:
    int                     m_nFileCurLine;
    int                     m_nFileMaxLine;
    string                  m_szFilename;
};
#endif
