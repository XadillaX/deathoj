#include "StdAfx.h"
#include "NCompiler_GCC.h"
#include "NCompiler_GPP.h"
#include "CPFactories.h"

namespace
{
    /** ����ģʽ֮�������� */
    NCompiler *CreateCP_GCC() { return new NCompiler_GCC; }
    NCompiler *CreateCP_GPP() { return new NCompiler_GPP; }

    /** ע�ᳵ�� */
    bool flag1 = CPFactories::Instance().Register("GCC", CreateCP_GCC);
    bool flag2 = CPFactories::Instance().Register("G++", CreateCP_GPP);
}
