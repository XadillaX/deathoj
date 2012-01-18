using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;
using System.IO;

namespace NBUTOJCoreCSharp
{
    enum NState
    {
        QUEUING,                        ///< 评测队列中
        COMPILING,                      ///< 编译中
        RUNNING,                        ///< 运行中
        ACCEPTED,                       ///< 通过
        PRESENTATION_ERROR,             ///< 格式错误
        WRONG_ANSWER,                   ///< 答案错误
        RUNTIME_ERROR,                  ///< 运行时错误
        TIME_LIMIT_EXCEEDED_1,          ///< 运行超时
        TIME_LIMIT_EXCEEDED_2,          ///< 运行超时
        MEMORY_LIMIT_EXCEEDED,          ///< 内存超出
        OUTPUT_LIMIT_EXCEEDED,          ///< 输出过长
        COMPILATION_ERROR,              ///< 编译错误
        COMPILATION_SUC,                ///< 编译通过
        SYSTEM_ERROR,                   ///< 系统错误
        OUT_OF_CONTEST_TIME,            ///< 超出比赛时间
        DANGEROUS_CODE                  ///< 危险代码
    }

    struct CodeState
    {
        public NState state;
        public string err_code;
        public int exe_time;
        public int exe_memory;
    }

    class NBUTOJCore
    {
        [DllImport("NBUTOJCore.dll")]
        public static extern string NBUTOJ_GetCurrentDirC();

        [DllImport("NBUTOJCore.dll")]
        private static extern bool NBUTOJ_Compile(string type, string input, string output);

        [DllImport("NBUTOJCore.dll")]
        public static extern int NBUTOJ_GetLastStateCode();

        [DllImport("NBUTOJCore.dll")]
        public static extern string NBUTOJ_GetLastError();

        [DllImport("NBUTOJCore.dll")]
        public static extern int NBUTOJ_GetLastTime();

        [DllImport("NBUTOJCore.dll")]
        public static extern int NBUTOJ_GetLastMemo();

        [DllImport("NBUTOJCore.dll")]
        private static extern bool NBUTOJ_Judge(string exe, string stdipt, string stdopt, int lim_time, int lim_memo);

        public string[] list = {
            "QUEUING",
            "COMPILING",
            "RUNNING",
            "ACCEPTED",
            "PRESENTATION_ERROR",
            "WRONG_ANSWER",
            "RUNTIME_ERROR",
            "TIME_LIMIT_EXCEEDED_1",
            "TIME_LIMIT_EXCEEDED_2",
            "MEMORY_LIMIT_EXCEEDED",
            "OUTPUT_LIMIT_EXCEEDED",
            "COMPILATION_ERROR",
            "COMPILATION_SUC",
            "SYSTEM_ERROR",
            "OUT_OF_CONTEST_TIME",
            "DANDEROUS_CODE"
        };

        public CodeState RealCompile(string type, string input, string output)
        {
            CodeState ans = new CodeState();
            bool result = NBUTOJ_Compile(type, input, output);
            if (!result)
            {
                ans.state = NState.COMPILATION_ERROR;
                ans.err_code = NBUTOJ_GetLastError();
                ans.exe_time = ans.exe_memory = 0;
                return ans;
            }

            ans.state = NState.COMPILATION_SUC;
            ans.exe_memory = ans.exe_time = 0;
            return ans;
        }

        public CodeState RealJudge(string exe, string stdipt, string stdopt, int lim_time, int lim_memo)
        {
            bool result = NBUTOJ_Judge(exe, stdipt, stdopt, lim_time, lim_memo);

            CodeState ans = new CodeState();
            ans.state = (NState)NBUTOJ_GetLastStateCode();
            ans.exe_time = NBUTOJ_GetLastTime();
            ans.exe_memory = NBUTOJ_GetLastMemo();

            if (ans.state == NState.RUNTIME_ERROR || ans.state == NState.COMPILATION_ERROR)
            {
                ans.err_code = NBUTOJ_GetLastError();
            }

            return ans;
        }
    }
}
