using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using NBUTOJCoreCSharp;
using System.Windows.Forms;
using System.IO;

namespace CoreCSharpTest
{
    public partial class Form1 : Form
    {
        private NBUTOJCore core = new NBUTOJCore();

        public Form1()
        {
            InitializeComponent();
        }

        private void button1_Click(object sender, EventArgs e)
        {
            richTextBox1.SaveFile("tmpdir/csharp_temp.cpp", RichTextBoxStreamType.PlainText);

            string std = textBox1.Text;
            string exe = System.Guid.NewGuid().ToString() + ".exe";

            CodeState state = core.RealCompile("G++", "csharp_temp.cpp", exe);
            if (state.state == NState.COMPILATION_ERROR)
            {
                File.Delete("tmpdir/csharp_temp.cpp");
                MessageBox.Show("编译失败: " + state.err_code);
                return;
            }

            state = core.RealJudge(exe, std + ".in", std + ".out", 1000, 65535);
            MessageBox.Show(
                "状态: " + core.list[(int)state.state] + "\n" +
                "时间: " + state.exe_time + " ms\n" +
                "内存: " + state.exe_memory + " kb\n" +
                "代码长度: " + richTextBox1.Text.Length
                );

            File.Delete("tmpdir/csharp_temp.cpp");
            File.Delete(exe);
        }
    }
}
