#include "StdAfx.h"
#include "NCompiler_GCC.h"
#include "NCompiler_GPP.h"
#include "CPFactories.h"

namespace
{
    /** 工厂模式之工厂函数 */
    NCompiler *CreateCP_GCC() { return new NCompiler_GCC; }
    NCompiler *CreateCP_GPP() { return new NCompiler_GPP; }

    /** 注册车间 */
    bool flag1 = CPFactories::Instance().Register("GCC", CreateCP_GCC);
    bool flag2 = CPFactories::Instance().Register("G++", CreateCP_GPP);
}
