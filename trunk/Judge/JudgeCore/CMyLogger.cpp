#include "CMyLogger.h"
#include <ctime>

CMyLogger::CMyLogger(void) :
    m_nFileMaxLine(1000),
    m_nFileCurLine(0),
    m_szFilename("")
{
    /** 新建文件 */
    CreateNextFile();
}

CMyLogger::~CMyLogger(void)
{
}

void CMyLogger::SetMaxLine(int line)
{
    m_nFileMaxLine = line;
}

string CMyLogger::GetFilename()
{
    return m_szFilename;
}

bool CMyLogger::CreateNextFile()
{
    /** 新建文件 */
    string filename = CreateFilename();
    FILE* fp = fopen(filename.c_str(), "w+");
    if(NULL == fp)
    {
        m_nFileCurLine = m_nFileMaxLine;
        return false;
    }
    fclose(fp);

    /** 更新索引 */
    char indexname[512] = "index.html";
    fp = fopen(indexname, "a+");
    if(NULL == fp)
    {
        return true;
    }
    fprintf(fp, "<a href='%s' target='_bland'>%s</a><br />\n", filename.c_str(), filename.c_str());
    fclose(fp);

    m_nFileCurLine = 0;
    m_szFilename = filename;

    return true;
}

string CMyLogger::CreateFilename()
{
    char filename[512];
    sprintf(filename, "%sLOGFILE[%s].html", LOG_PATH, GetCurTime('-', '-').c_str());
    return filename;
}

string CMyLogger::GetCurTime(char spliter1, char spliter2)
{
    time_t sysTime = time(NULL);
    tm* tagTime;

    tagTime = localtime(&sysTime);
    char output[20];
    sprintf(
        output,
        "%.2d%c%.2d%c%.2d %.2d%c%.2d%c%.2d",
        tagTime->tm_year + 1900,
        spliter1,
        tagTime->tm_mon + 1,
        spliter1,
        tagTime->tm_mday,
        tagTime->tm_hour,
        spliter2,
        tagTime->tm_min,
        spliter2,
        tagTime->tm_sec
        );

    return output;
}

void CMyLogger::SetLog(string title, string body, bool titlebold, bool bodybold, string titlecolor, string bodycolor)
{
    string curTime = GetCurTime();

    /** 屏幕输出 */
    printf("[%s]%s: %s\n", curTime.c_str(), title.c_str(), body.c_str());

    /** 若输出已满则新建文件 */
    if(m_nFileMaxLine <= m_nFileCurLine)
    {
        if(!CreateNextFile()) return;
    }

    /** 生成字符串 */
    string output = "[" + curTime + "]<span style='color: " + titlecolor + ";";
    if(titlebold)
    {
        output += " font-weight: bold;";
    }
    output += ("'>" + title + "</span>");

    if(body != "")
    {
        output += (": <span style='color: " + bodycolor + ";");
        if(bodybold)
        {
            output += " font-weight: bold;";
        }
        output += ("'>" + body + "</span>");
    }

    output += "<br />";

    /** 输出到文件 */
    FILE* fp = fopen(m_szFilename.c_str(), "a+");
    if(NULL == fp) return;
    fprintf(fp, "%s\n", output.c_str());
    fclose(fp);
    m_nFileCurLine++;
}
