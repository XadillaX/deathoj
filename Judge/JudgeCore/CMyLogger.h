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

    void                    SetMaxLine(int line);                       ///< 设置历史文件最大行数
    string                  GetFilename();                              ///< 获取当前历史文件名
    bool                    CreateNextFile();                           ///< 创建下一个历史文件
    string                  CreateFilename();                           ///< 创建一个历史文件名
    string                  GetCurTime(char spliter1 = '-', char spliter2 = ':');

private:
    int                     m_nFileCurLine;
    int                     m_nFileMaxLine;
    string                  m_szFilename;
};
#endif
