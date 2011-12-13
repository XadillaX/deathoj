namespace NBUTOJClient
{
    partial class NBUTOJForm
    {
        /// <summary>
        /// 必需的设计器变量。
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// 清理所有正在使用的资源。
        /// </summary>
        /// <param name="disposing">如果应释放托管资源，为 true；否则为 false。</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows 窗体设计器生成的代码

        /// <summary>
        /// 设计器支持所需的方法 - 不要
        /// 使用代码编辑器修改此方法的内容。
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(NBUTOJForm));
            this.panel1 = new System.Windows.Forms.Panel();
            this.Browser = new System.Windows.Forms.WebBrowser();
            this.goback = new System.Windows.Forms.Button();
            this.goforward = new System.Windows.Forms.Button();
            this.gohome = new System.Windows.Forms.Button();
            this.refresh = new System.Windows.Forms.Button();
            this.TimeLabel = new System.Windows.Forms.Label();
            this.TimeLabelTimer = new System.Windows.Forms.Timer(this.components);
            this.panel1.SuspendLayout();
            this.SuspendLayout();
            // 
            // panel1
            // 
            this.panel1.BackColor = System.Drawing.Color.White;
            this.panel1.Controls.Add(this.TimeLabel);
            this.panel1.Controls.Add(this.refresh);
            this.panel1.Controls.Add(this.gohome);
            this.panel1.Controls.Add(this.goforward);
            this.panel1.Controls.Add(this.goback);
            this.panel1.Dock = System.Windows.Forms.DockStyle.Top;
            this.panel1.Location = new System.Drawing.Point(0, 0);
            this.panel1.Name = "panel1";
            this.panel1.Size = new System.Drawing.Size(1008, 32);
            this.panel1.TabIndex = 0;
            // 
            // Browser
            // 
            this.Browser.AllowWebBrowserDrop = false;
            this.Browser.Dock = System.Windows.Forms.DockStyle.Fill;
            this.Browser.IsWebBrowserContextMenuEnabled = false;
            this.Browser.Location = new System.Drawing.Point(0, 32);
            this.Browser.MinimumSize = new System.Drawing.Size(20, 20);
            this.Browser.Name = "Browser";
            this.Browser.Size = new System.Drawing.Size(1008, 630);
            this.Browser.TabIndex = 1;
            this.Browser.Navigated += new System.Windows.Forms.WebBrowserNavigatedEventHandler(this.Browser_Navigated);
            // 
            // goback
            // 
            this.goback.Cursor = System.Windows.Forms.Cursors.Hand;
            this.goback.Enabled = false;
            this.goback.FlatAppearance.BorderSize = 0;
            this.goback.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.goback.Font = new System.Drawing.Font("微软雅黑", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.goback.ForeColor = System.Drawing.Color.White;
            this.goback.Location = new System.Drawing.Point(12, 0);
            this.goback.Name = "goback";
            this.goback.Size = new System.Drawing.Size(32, 32);
            this.goback.TabIndex = 0;
            this.goback.Text = "☜";
            this.goback.UseVisualStyleBackColor = true;
            this.goback.Click += new System.EventHandler(this.goback_Click);
            // 
            // goforward
            // 
            this.goforward.Cursor = System.Windows.Forms.Cursors.Hand;
            this.goforward.Enabled = false;
            this.goforward.FlatAppearance.BorderSize = 0;
            this.goforward.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.goforward.Font = new System.Drawing.Font("微软雅黑", 12F);
            this.goforward.ForeColor = System.Drawing.Color.White;
            this.goforward.Location = new System.Drawing.Point(50, 0);
            this.goforward.Name = "goforward";
            this.goforward.Size = new System.Drawing.Size(32, 32);
            this.goforward.TabIndex = 1;
            this.goforward.Text = "☞";
            this.goforward.UseVisualStyleBackColor = true;
            this.goforward.Click += new System.EventHandler(this.goforward_Click);
            // 
            // gohome
            // 
            this.gohome.Cursor = System.Windows.Forms.Cursors.Hand;
            this.gohome.FlatAppearance.BorderSize = 0;
            this.gohome.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.gohome.Font = new System.Drawing.Font("微软雅黑", 12F);
            this.gohome.ForeColor = System.Drawing.Color.White;
            this.gohome.Location = new System.Drawing.Point(88, 0);
            this.gohome.Name = "gohome";
            this.gohome.Size = new System.Drawing.Size(32, 32);
            this.gohome.TabIndex = 2;
            this.gohome.Text = "۩";
            this.gohome.UseVisualStyleBackColor = true;
            this.gohome.Click += new System.EventHandler(this.gohome_Click);
            // 
            // refresh
            // 
            this.refresh.Cursor = System.Windows.Forms.Cursors.Hand;
            this.refresh.FlatAppearance.BorderSize = 0;
            this.refresh.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.refresh.Font = new System.Drawing.Font("微软雅黑", 12F);
            this.refresh.ForeColor = System.Drawing.Color.White;
            this.refresh.Location = new System.Drawing.Point(126, 0);
            this.refresh.Name = "refresh";
            this.refresh.Size = new System.Drawing.Size(32, 32);
            this.refresh.TabIndex = 3;
            this.refresh.Text = "❉";
            this.refresh.UseVisualStyleBackColor = true;
            this.refresh.Click += new System.EventHandler(this.refresh_Click);
            // 
            // TimeLabel
            // 
            this.TimeLabel.AutoSize = true;
            this.TimeLabel.Font = new System.Drawing.Font("微软雅黑", 12F);
            this.TimeLabel.Location = new System.Drawing.Point(164, 6);
            this.TimeLabel.Name = "TimeLabel";
            this.TimeLabel.Size = new System.Drawing.Size(270, 21);
            this.TimeLabel.TabIndex = 4;
            this.TimeLabel.Text = "当前客户机时间: xxxx-xx-xx xx:xx:xx";
            // 
            // TimeLabelTimer
            // 
            this.TimeLabelTimer.Enabled = true;
            this.TimeLabelTimer.Interval = 1000;
            this.TimeLabelTimer.Tick += new System.EventHandler(this.TimeLabelTimer_Tick);
            // 
            // NBUTOJForm
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(1008, 662);
            this.Controls.Add(this.Browser);
            this.Controls.Add(this.panel1);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.Name = "NBUTOJForm";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "NBUT Online Judge Syatem";
            this.Load += new System.EventHandler(this.NBUTOJForm_Load);
            this.panel1.ResumeLayout(false);
            this.panel1.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Panel panel1;
        private System.Windows.Forms.WebBrowser Browser;
        private System.Windows.Forms.Button goback;
        private System.Windows.Forms.Button goforward;
        private System.Windows.Forms.Button gohome;
        private System.Windows.Forms.Button refresh;
        private System.Windows.Forms.Label TimeLabel;
        private System.Windows.Forms.Timer TimeLabelTimer;
    }
}

