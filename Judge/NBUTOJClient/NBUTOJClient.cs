using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace NBUTOJClient
{
    public partial class NBUTOJForm : Form
    {
        private string root_url = "http://acm.nbut.cn/";

        public NBUTOJForm()
        {
            InitializeComponent();

            goback.BackColor = Color.FromArgb(0x8e, 0xe1, 0xff);
            goforward.BackColor = Color.FromArgb(0x8e, 0xe1, 0xff);
            gohome.BackColor = Color.FromArgb(0x8e, 0xe1, 0xff);
            refresh.BackColor = Color.FromArgb(0x8e, 0xe1, 0xff);
            panel1.BackColor = Color.FromArgb(0x44, 0xcc, 0xff);

            TimeLabelTimer_Tick(this, null);
        }

        private void NBUTOJForm_Load(object sender, EventArgs e)
        {
            Browser.Url = new Uri(root_url);
        }

        private void goback_Click(object sender, EventArgs e)
        {
            Browser.GoBack();
        }

        private void goforward_Click(object sender, EventArgs e)
        {
            Browser.GoForward();
        }

        private void Browser_Navigated(object sender, WebBrowserNavigatedEventArgs e)
        {
            goback.Enabled = Browser.CanGoBack;
            goforward.Enabled = Browser.CanGoForward;
        }

        private void gohome_Click(object sender, EventArgs e)
        {
            Browser.Url = new Uri(root_url);
        }

        private void refresh_Click(object sender, EventArgs e)
        {
            Browser.Refresh();
        }

        private void TimeLabelTimer_Tick(object sender, EventArgs e)
        {
            DateTime dt = DateTime.Now;
            TimeLabel.Text = "当前客户机时间: ";
            TimeLabel.Text += (dt.Year.ToString() + "-" + dt.Month.ToString() + "-" + dt.Day.ToString() + " ");
            TimeLabel.Text += (dt.Hour.ToString() + ":" + dt.Minute.ToString() + ":" + dt.Second.ToString());
        }
    }
}
