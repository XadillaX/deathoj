#include "StdAfx.h"
#include <io.h>
#include <stdlib.h>
#include "NCompiler.h"

/**
 * @brief ���캯��
 */
NCompiler::NCompiler(void)
{

}

/**
 * @brief ��������
 */
NCompiler::~NCompiler(void)
{

}

/**
 * @brief ����������
 * ����һ�����ڱ��������������ļ�
 *
 * @param cmd ������������
 * @return ���������ؽ��������trueΪ�����ɹ���falseΪ����ʧ��
 */
bool NCompiler::MakeBat(const char *cmd)
{
    FILE *fp;
    FILE *lock;                     ///< �������ļ�
    if(NULL == (fp = fopen(BAT_FILENAME, "w+")) ||
        NULL == (lock = fopen(LOCK_FILENAME, "w+")))
    {
        fclose(fp);
        fclose(lock);
        return false;
    }

    /** ����������� */
    fprintf(fp, "%s", cmd);

    /** ���Ա�����ʱ����ʱָ�� */
    //fprintf(fp, "ping 127.0.0.1\n");

    /** ���ɾ������������ */
    fprintf(fp, "del %s\n", LOCK_FILENAME);

    fclose(fp);
    fclose(lock);

    return true;
}

/**
 * @brief ���뺯��
 * �麯�������ڼ̳�
 *
 * @param input �����ļ���
 * @param output �����ļ���
 * @param err_code ���ڽ��մ�����Ϣ���ַ���
 * @return ���������ر�������trueΪ����ɹ���falseΪ����ʧ��
 *
 * @see _CompileFile(char *err_code)
 */
bool NCompiler::CompileFile(const char *input, const char *output, char *err_code)
{
    /** Todo: */
    return true;
}

/**
 * @brief ͨ�ñ��뺯��
 * NCompiler����̳����ͨ�ñ��뺯��
 *
 * @param err_code ���ڽ��մ�����Ϣ���ַ���
 * @return ���������ر�������trueΪ����ɹ���falseΪ����ʧ��
 */
bool NCompiler::_CompileFile(char *err_code)
{
    /** ����bat�ļ����б��� */
    HINSTANCE hIns = ShellExecute(NULL,
            "open",
            BAT_FILENAME,
            "",
            "",
            SW_HIDE
        );

    //cout << hIns << endl;
    /** ��ShellExecute���ؾ������ */
    if(hIns <= (HINSTANCE)(32))
    {
        strcpy(err_code, "System error.");
        return false;
    }

    //time_t t;
    //time(&t);
    //cout << "���뿪ʼʱ�䣺" << t << endl;
    
    /** �ȴ��������ļ���ɾ�� */
    while(0 == access(LOCK_FILENAME, 0)) Sleep(1);

    //time(&t);
    //cout << "�������ʱ�䣺" << t << endl;

    /** ��ȡ������Ϣ */
    FILE *err_file;
    if(NULL == (err_file = fopen(ERR_FILENAME, "r")))
    {
        strcpy(err_code, "System error.");
        return false;
    }

    /** ��ȡerr_file */
    char c[10240];
    int i = 0;
    while(!feof(err_file) && i < 10000)
    {
        c[i++] = fgetc(err_file);
    }
    c[i] = '\0';
    fclose(err_file);

    strcpy(err_code, c);
    ::DeleteFile(ERR_FILENAME);
    ::DeleteFile(BAT_FILENAME);
    //strcpy(err_code, "\0");

    return true;
}

/**
 * @brief ���˺���
 * ���˲���ȫ�Ĵ���
 *
 * @param *input �����ļ���
 * @param *filter_filename ���˹����ļ���
 * @return �����Ƿ���ˣ�������ȫ���룩��trueΪ�����ˣ�falseΪû������
 *
 * @note �麯�������ڼ̳�
 */
bool NCompiler::FilterCode(const char *input)
{

    return false;
}