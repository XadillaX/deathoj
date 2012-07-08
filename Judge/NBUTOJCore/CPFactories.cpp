#include "StdAfx.h"
#include "NCompiler_GCC.h"
#include "NCompiler_GPP.h"
#include "NCompiler_FPC.h"
#include "NCompiler_JAVA.h"
#include "CPFactories.h"

namespace
{
    /** ����ģʽ֮�������� */
    NCompiler *CreateCP_GCC() { return new NCompiler_GCC; }
    NCompiler *CreateCP_GPP() { return new NCompiler_GPP; }
    NCompiler *CreateCP_FPC() { return new NCompiler_FPC; }
    NCompiler *CreateCP_JAVA() { return new NCompiler_JAVA; }

    /** ע�ᳵ�� */
    bool flag1 = CPFactories::Instance().Register("GCC", CreateCP_GCC);
    bool flag2 = CPFactories::Instance().Register("G++", CreateCP_GPP);
    bool flag3 = CPFactories::Instance().Register("FPC", CreateCP_FPC);
}
