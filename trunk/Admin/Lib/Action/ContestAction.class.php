<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-12-5 上午8:25
 */

/**
 * @brief 比赛控制器
 * 比赛添加、编辑、删除
 */
class ContestAction extends CommonAction
{
    private $problem_model;
    private $contest_model;
    private $contestproblem_model;
    private $contestuser_model;
    private $submit_model;

    public function __construct()
    {
        parent::__construct();

        $this->problem_model = new ProblemModel("problem");
        $this->contest_model = new ContestModel("contest");
        $this->contestproblem_model = new ContestProblemModel("contestproblem");
        $this->contestuser_model = new ContestUserModel("contestuser");
        $this->submit_model = new SubmitModel("submit");
    }

    /**
     * 添加比赛
     * @return void
     */
    public function add_contest()
    {
        $this->web_config["title"] .= " 添加比赛";

        /** 所属分类 */
        $this->web_config["action_class"] = "contest";
        $this->web_config["sub_action"] = "new";

        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        $this->display();
    }

    /**
     * 检验添加比赛
     * @return void
     */
    public function chkadd_contest()
    {
        $data = (array)json_decode($_POST["data"]);

        /** 检验 */
        if(!$this->problem_model->autoCheckToken($data))
        {
            $this->error("非法提交！", true);
            die(0);
        }
        if(null == $data["title"] || "" == trim($data["title"]))
        {
            $this->error("标题不能留空。", true);
            die(0);
        }

        /** 数据 */
        $real_data = array();
        $real_data["title"] = $data["title"];
        $real_data["private"] = $data["private"];
        $real_data["language"] = $data["language"];
        $real_data["starttime"] = ($data["starttime"] == "") ? null : strtotime($data["starttime"]);
        $real_data["endtime"] = ($data["endtime"] == "") ? null : strtotime($data["endtime"]);
        $real_data["description"] = $data["description"];
        $real_data["addtime"] = time();
        $real_data["submit"] = 0;

        /** 写入数据库 */
        $result = $this->contest_model->add_contest($real_data);
        if(false === $result)
        {
            $this->error("系统错误，请联系开发人员或者稍后再试。", true);
            die(0);
        }
        else
        {
            $this->success("添加成功，比赛ID: " . $result, true);
            die(0);
        }
    }

    /**
     * 确认编辑信息
     * @return void
     */
    public function chkedit_contest()
    {
        $contestid = $_GET["contestid"];

        $data = (array)json_decode($_POST["data"]);

        /** 检验 */
        if(!$this->problem_model->autoCheckToken($data) || !is_numeric($contestid))
        {
            $this->error("非法提交！", true);
            die(0);
        }
        if(null == $data["title"] || "" == trim($data["title"]))
        {
            $this->error("标题不能留空。", true);
            die(0);
        }

        /** 数据 */
        $real_data = array();
        $real_data["title"] = $data["title"];
        $real_data["private"] = $data["private"];
        $real_data["language"] = $data["language"];
        $real_data["starttime"] = ($data["starttime"] == "") ? null : strtotime($data["starttime"]);
        $real_data["endtime"] = ($data["endtime"] == "") ? null : strtotime($data["endtime"]);
        $real_data["description"] = $data["description"];
        $real_data["addtime"] = time();

        /** 更新数据库 */
        $result = $this->contest_model->edit_contest($contestid, $real_data);
        if(false == $result)
        {
            $this->error("系统错误，请联系开发人员或者稍后再试。", true);
            die(0);
        }
        else
        {
            $this->success("修改成功。", true);
            die(0);
        }
    }

    /**
     * 编辑比赛信息
     * @return void
     */
    public function edit_contest()
    {
        $this->web_config["title"] .= " 编辑比赛";

        /** 所属分类 */
        $this->web_config["action_class"] = "contest";
        $this->web_config["sub_action"] = "contest";
        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        /** 获取题目信息 */
        $contestid = $_GET["contestid"];
        if($contestid === "" || $contestid === null || $contestid === 1 || !is_numeric($contestid))
        {
            redirect(U("Contest/catalog") . "?page=" . Session::get("contest_page_when_back"));
            die(0);
        }
        $data = $this->contest_model->get_contest_info($contestid);
        if(false == $data)
        {
            redirect(U("Contest/catalog") . "?page=" . Session::get("contest_page_when_back"));
            die(0);
        }
        $this->assign("data", $data);

        $this->display();
    }

    /**
     * 比赛目录控制器
     * @version $Id$
     * @return void
     */
    public function catalog()
    {
        /** 页码 */
        $page_num = $_GET["page"];
        if(null == $page_num || !is_numeric($page_num)) $page_num = 1;
        $per_page = C("CONTEST_NUM_PER_PAGE");
        $tot_num = $this->contest_model->count();
        $pages = (int)((int)$tot_num / (int)$per_page) + (($tot_num % $per_page == 0) ? 0 : 1);
        if($page_num > $pages) $page_num = $pages;
        $_GET["page"] = $page_num;
        $this->web_config["title"] .= " 比赛列表 :: 第 " . $page_num . " 页";
        Session::set("contest_page_when_back", $page_num);

        /** 所属分类 */
        $this->web_config["action_class"] = "contest";
        $this->web_config["sub_action"] = "contest";
        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        /** 页码字符串 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("Contest/catalog") . "?page=%s";
        $page_obj->per_page = $per_page;                ///< 每页数量
        $page_obj->item_count = $tot_num;               ///< 记录数
        $page_obj->cur_page = $page_num;                ///< 当前页码
        $page_str = $page_obj->create_links();
        $this->assign("page_str", $page_str);

        /** 题目们 */
        $data = $this->contest_model->get_catalog($page_num, $per_page);
        $this->assign("cat_data", $data);

        /** 哥要显示啦~ */
        $this->display();
    }

    /**
     * 题目列表控制器
     * @version $Id$
     * @return void
     */
    public function prob_list()
    {
        /** 比赛编号 */
        $contestid = $_GET["contestid"];
        if(!is_numeric($contestid))
        {
            redirect(U("Contest/catalog") . "?page=" . Session::get("contest_page_when_back"));
            die(0);
        }
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false === $contest_info)
        {
            redirect(U("Contest/catalog") . "?page=" . Session::get("contest_page_when_back"));
            die(0);
        }

        /** 读取题目列表 */
        $prob_list = $this->contestproblem_model->get_all_problems($contestid);

        /** ASSIGN数据 */
        $this->web_config["action_class"] = "contest";
        $this->web_config["sub_action"] = "contest";
        $this->web_config["title"] .= " 题目列表 :: {$contest_info['title']}";
        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        $this->assign("contest_info", $contest_info);
        $this->assign("prob_list", $prob_list);

        /** 输出模板 */
        $this->display();
    }

    /**
     * 添加题目
     * @version $Id$
     * @return void
     */
    public function add_prob()
    {
        $index = $_POST["index"];
        $problemid = $_POST["problemid"];
        $contestid = $_POST["contestid"];

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false === $contest_info)
        {
            $this->error("不存在的比赛。", true);
        }

        /** 是否有这个题目 */
        $result = $this->problem_model->get_problem_by_id($problemid);
        if(false == $result)
        {
            $this->error("不存在的题目编号。", true);
            die(0);
        }

        /** 是否有练习题库 */
        $info = $this->contest_model->get_contest_info($contestid);
        if(false === $info)
        {
            $this->error("不存在练习题库。", true);
            die(0);
        }

        $result = $this->contestproblem_model->add_problem($contestid, $index, $problemid);
        if(false === $result)
        {
            $this->error("系统错误，可能是已存在这个索引。", true);
            die(0);
        }
        else
        {
            $this->success("添加成功！", $result);
            die(0);
        }
    }

    /**
     * 确认生成队伍
     * @return void
     */
    public function chk_gen_team()
    {
        $username_prefix = $_POST["username_prefix"];
        $fill_len = $_POST["fill_len"];
        $team_prefix = $_POST["team_prefix"];
        $team_len = $_POST["team_len"];
        $count = $_POST["count"];

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($_GET["contestid"]);
        if(false === $contest_info)
        {
            $this->alert_redirect("不存在的比赛。");
            die(0);
        }

        /** 是否已经生成过 */
        if(null != $this->contestuser_model->get_user_list($_GET["contestid"]))
        {
            $this->alert_redirect("此比赛已经生成过队伍了，如果需要再次生成，请先清除原先队伍数据。");
            die(0);
        }

        /** COUNT不合法 */
        if(!is_numeric($count) || $count < 1 || $count > 5000)
        {
            $this->alert_redirect("队伍数量不合法。");
            die(0);
        }

        /** 令牌验证不通过 */
        if(!$this->contestuser_model->autoCheckToken($_POST))
        {
            $this->alert_redirect("令牌验证不通过，非法提交。", U("Contest/generate_team") . "?contestid={$_GET['contestid']}");
            die(0);
        }

        /** 长度不符合要求 */
        if(strlen($username_prefix) + $fill_len > 16)
        {
            $this->alert_redirect("将生成的用户名长度超过16位。");
            die(0);
        }
        if(strlen($team_prefix) + max($team_len, strlen($count)) > 16)
        {
            $this->alert_redirect("将生成的队名长度超过16位。");
            die(0);
        }

        /** 开始生成 */
        $dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $new_user_info = array();
        $new_username = array();
        $new_team_info = array();
        for($i = 0; $i < $count; $i++)
        {
            /** 随机生成用户名 */
            $new_user_info[$i]["username"] = "";
            $new_user_info[$i]["username"] = $username_prefix;
            for($j = 0; $j < $fill_len; $j++)
            {
                $new_user_info[$i]["username"] .= $dict[mt_rand() % strlen($dict)];
            }

            /** 在用户名数组中找 */
            if(in_array($new_user_info[$i]["username"], $new_username))
            {
                $i--;
                continue;
            }

            /** 数据库中找,若已存在，则重来一遍 */
            $temp = $this->MUser->get_user_info("username", $new_user_info[$i]["username"]);
            if(NULL != $temp)
            {
                $i--;
                continue;
            }

            /** 随机密码[八位] */
            $new_user_info[$i]["password"] = "";
            for($j = 0; $j < 8; $j++)
            {
                $new_user_info[$i]["password"] .= $dict[mt_rand() % strlen($dict)];
            }

            /** 队名 */
            $num = $i + 1;
            $new_user_info[$i]["teamname"] = "";
            $new_user_info[$i]["teamname"] = $team_prefix;
            $temp_teamlen = max(strlen($num), $team_len);
            $zero_count = $temp_teamlen - strlen($num);
            for($j = 0; $j < $zero_count; $j++) $new_user_info[$i]["teamname"] .= "0";
            $new_user_info[$i]["teamname"] .= $num;

            /** 写入数据库 */
            $userid = $this->MUser->create_team_user($new_user_info[$i]["username"], $new_user_info[$i]["password"], $new_user_info[$i]["teamname"]);
            if(false === $userid)
            {
                /** 写入失败则重来一遍 */
                $i--;
                continue;
            }
            $new_username[$i] = $new_user_info[$i]["username"];

            /** 队伍信息数组 */
            $new_team_info[$i]["userid"] = $userid;
            $new_team_info[$i]["teamname"] = $new_user_info[$i]["teamname"];
        }

        /** 写入队伍信息 */
        $result = $this->contestuser_model->add_user_list($_GET["contestid"], $new_team_info);
        if(false === $result)
        {
            //echo $this->contestuser_model->getLastSql();
            $this->alert_redirect("系统错误：生成队伍写入数据库时出错。");
            die(0);
        }
        else
        {
            //echo $this->contestuser_model->getLastSql();
            $this->alert_redirect("生成成功。", U("Contest/generate_team") . "?contestid={$_GET['contestid']}");
            die(0);
        }
    }

    /**
     * 生成队伍
     * @return void
     */
    public function generate_team()
    {
        /** 比赛编号 */
        $contestid = $_GET["contestid"];
        if(!is_numeric($contestid))
        {
            redirect(U("Contest/catalog") . "?page=" . Session::get("contest_page_when_back"));
            die(0);
        }
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false === $contest_info)
        {
            redirect(U("Contest/catalog") . "?page=" . Session::get("contest_page_when_back"));
            die(0);
        }

        /** @important 队伍信息 : 生成的用户密码不用MD5加密，所以必须小于32位 */
        $user_list = $this->contestuser_model->get_user_list($contestid);

        if($user_list === false || $user_list === NULL)
        {
            /** ASSIGN数据 */
            $this->web_config["action_class"] = "contest";
            $this->web_config["sub_action"] = "contest";
            $this->web_config["title"] .= " 生成队伍 :: {$contest_info['title']}";
            $this->assign("HC", $this->web_config);
            $this->assign("admin_information", $this->admin_information);
            $this->assign("contest_info", $contest_info);

            /** 无用户 */
            $this->display("generate_team_no_user");
        }
        else
        {
            /** ASSIGN数据 */
            $this->web_config["action_class"] = "contest";
            $this->web_config["sub_action"] = "contest";
            $this->web_config["title"] .= " 队伍信息 :: {$contest_info['title']}";
            $this->assign("HC", $this->web_config);
            $this->assign("admin_information", $this->admin_information);
            $this->assign("contest_info", $contest_info);
            $this->assign("team_list", $user_list);

            /** 有用户 */
            $this->display("generate_team_list");
        }
    }

    /**
     * 导出队伍列表EXCEL
     * @return void
     */
    public function download_team_excel()
    {
        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($_GET["contestid"]);
        if(false === $contest_info)
        {
            $this->alert_redirect("不存在的比赛。");
            die(0);
        }

        /* EXCEL类库 **/
        import("@.Plugin.PHPExcel.PHPExcel");

        /** EXCEL对象 */
        $objExcel = new PHPExcel();
        $objWriter = new PHPExcel_Writer_Excel5($objExcel);

        /** EXCEL属性  */
        $objProps = $objExcel->getProperties();
        $objProps->setCreator($this->web_config["webname"]);
        $objProps->setLastModifiedBy("Zhu Kaidi");
        $objProps->setTitle($this->web_config["webname"]);

        /** SHEET页 */
        $objExcel->setActiveSheetIndex(0);
        $objActSheet = $objExcel->getActiveSheet();
        $objActSheet->setTitle("参赛队伍");

        $objActSheet->setCellValue("A1", "用户编号");
        $objActSheet->setCellValue("B1", "用户名");
        $objActSheet->setCellValue("C1", "密码");
        $objActSheet->setCellValue("D1", "队名");
        $objActSheet->setCellValue("E1", "备注");

        /** 获取列表 */
        $user_list = $this->contestuser_model->get_user_list($_GET["contestid"]);
        for($i = 0; $i < count($user_list); $i++)
        {
            $row = $i + 2;
            $objActSheet->setCellValue("A" . $row, $user_list[$i]["userid"]);
            $objActSheet->setCellValue("B" . $row, $user_list[$i]["username"]);
            $objActSheet->setCellValue("C" . $row, $user_list[$i]["password"]);
            $objActSheet->setCellValue("D" . $row, $user_list[$i]["teamname"]);
        }
        $objActSheet->getColumnDimension('A')->setAutoSize(true);
        $objActSheet->getColumnDimension('B')->setAutoSize(true);
        $objActSheet->getColumnDimension('C')->setAutoSize(true);
        $objActSheet->getColumnDimension('D')->setAutoSize(true);

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="参赛队伍 - ' . $contest_info['title'] . '.xls"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");

        $objWriter->save('php://output');
    }

    /**
     * 清空队伍用户资料
     * @return void
     */
    public function clear_team()
    {
        $contestid = $_GET["contestid"];
        
        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false === $contest_info)
        {
            $this->alert_redirect("不存在的比赛。");
            die(0);
        }

        /** 比赛用户 */
        $contest_user_info = $this->contestuser_model->where(array("contestid" => $contestid))->select();
        if(null == $contest_user_info)
        {
            $this->alert_redirect("还木有用户呢。");
            die(0);
        }

        /** 用in来作condition */
        $condition["userid"] = array("in", array());
        for($i = 0; $i < count($contest_user_info); $i++)
        {
            $condition["userid"][1][$i] = $contest_user_info[$i]["userid"];
        }
        /** 删除USER表中的相应用户 */
        if(!$this->MUser->where($condition)->delete())
        {
            $this->alert_redirect("数据库错误，请稍后再试。");
            die(0);
        }
        /** 删除CONTESTUSER表中的相应用户 */
        $this->contestuser_model->where(array("contestid" => $contestid))->delete();
        $this->alert_redirect("清空成功。", U("Contest/generate_team") . "?contestid={$contestid}");
    }

    /**
     * Rejudge功能
     * @return void
     */
    public function rejudge()
    {
        $contestid = $_GET["contestid"];
        $state = $_GET["state"];

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false === $contest_info || $contestid == 1)
        {
            $this->alert_redirect("不存在的比赛。");
            die(0);
        }

        switch($state)
        {
        case "rejudge":
            {
                $this->contestproblem_model->rejudge($contestid);
                $this->submit_model->rejudge($contestid);
                $this->contest_model->rejudge($contestid);

                $re_mode = new Model("runtimeerror");
                $re_mode->where(array("contestid" => $contestid))->delete();

                $this->alert_redirect("Rejudge [{$contest_info['title']}] 成功。", U("Contest/catalog") . "?page=" . Session::get("contest_page_when_back"));

                break;
            }

        case "confirm":
        default:
            {
                /** ASSIGN数据 */
                $this->web_config["action_class"] = "contest";
                $this->web_config["sub_action"] = "contest";
                $this->web_config["title"] .= "Rejudge :: {$contest_info['title']}";
                $this->assign("HC", $this->web_config);
                $this->assign("admin_information", $this->admin_information);
                $this->assign("contest_info", $contest_info);
                $this->assign("back_page", Session::get("contest_page_when_back"));

                $this->display("rejudge");
                break;
            }
        }
    }
	
	
	private function removeDir($dirName) 
	{ 
		 if(!is_dir($dirName)) //如果传入的参数不是目录，则为文件，应将其删除
		 { 
		 @unlink($dirName);//删除文件
		   return false; 
		 } 
		 $handle = @opendir($dirName); //如果传入的参数是目录，则使用opendir将该目录打开，将返回的句柄赋值给$handle
		 while(($file = @readdir($handle)) !== false) //这里明确地测试返回值是否全等于（值和类型都相同）FALSE，否则任何目录项的名称求值为 FALSE 的都会导致循环停止（例如一个目录名为“0”）。 
		 { 
			 if($file!='.'&&$file!='..') //在文件结构中，都会包含形如“.”和“..”的向上结构，但是它们不是文件或者文件夹
			 {
			 $dir = $dirName . '/' . $file; //当前文件$dir为文件目录+文件
			 is_dir($dir)?$this->removeDir($dir):@unlink($dir); //判断$dir是否为目录，如果是目录则递归调用reMoveDir($dirName)函数，将其中的文件和目录都删除；如果不是目录，则删除该文件
			 } 
		 } 
		 closedir($handle); 
		  
		 return rmdir($dirName) ; 
	}
	
	/**
	 * 导出代码
	 * @version $Id$
	 */
	public function exp_code()
	{
		$contestid = $_GET["contestid"];
		
		/** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false === $contest_info || $contestid == 1)
        {
            $this->alert_redirect("不存在的比赛。");
            die(0);
        }
		
		/** 获取代码 */
		$submit_model = new SubmitModel("submit");
		
		$rst = $submit_model->get_contest_submit_info($contestid);
		
		if($rst == false)
		{
			echo "No code.";
			return;
		}

		$time = time();
		
		$root = C("RANK_PATH") . $contest_info["contestid"] . "-" . $time . "\\";
		mkdir($root, 0777);
		chmod($root, 0777);
		
		foreach($rst as $val)
		{
			$path = $root . $val["nickname"] . "[" . $val["username"] . "]\\";
            $path = iconv("UTF-8", "GB2312", $path);

			mkdir($path, 0777);
			chmod($path, 0777);
			
			$probpath = $path . $val["index"] . "\\";
			mkdir($probpath, 0777);
			chmod($probpath, 0777);
			
			$filename = $probpath . date("[Y-m-d H-i-s]", $val["submittime"]) . ".[" . $val["result"] . "]" . ($val["language"] == 1 ? ".c" : ".cpp");
            $fp = fopen($filename, "w+");
			
			fwrite($fp, $val["code"]);
			
			fclose($fp);
		}
		
		/** 打包 */
		import("@.Plugin.PHPZip");
		$zip = new PHPZip();
		$zip->ZipAndDownload($root, "Code.zip");
		
		$this->removeDir($root);
	}

    /**
     * 删除比赛
     * @return void
     */
    public function del_contest()
    {
        $contestid = $_GET["contestid"];
        $state = $_GET["state"];

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false === $contest_info || $contestid == 1)
        {
            $this->alert_redirect("不存在的比赛。");
            die(0);
        }

        /** 状态 */
        switch($state)
        {
        case "del":
            {
                /** !小心删除! */
                $condition = array("contestid" => $contestid);

                $code_model = new Model("code");
                $re_model = new Model("runtimeerror");

                $this->contestuser_model->del_all($contestid);          ///< 删除比赛用户表数据
                $this->submit_model->del_all($contestid);               ///< 删除提交表数据
                $code_model->where($condition)->delete();               ///< 删除代码表数据
                $re_model->where($condition)->delete();                 ///< 删除RE表数据
                $this->contestproblem_model->where($condition)->delete();   ///< 题目表数据
                $this->contest_model->where($condition)->delete();      ///< 比赛详细数据

                $this->alert_redirect("删除 [{$contest_info['title']}] 成功。", U("Contest/catalog") . "?page=" . Session::get("contest_page_when_back"));

                break;
            }

        case "confirm":
        default:
            {
                /** ASSIGN数据 */
                $this->web_config["action_class"] = "contest";
                $this->web_config["sub_action"] = "contest";
                $this->web_config["title"] .= " 删除比赛 :: {$contest_info['title']}";
                $this->assign("HC", $this->web_config);
                $this->assign("admin_information", $this->admin_information);
                $this->assign("contest_info", $contest_info);
                $this->assign("back_page", Session::get("contest_page_when_back"));

                $this->display("del_contest");

                break;
            }
        }
    }
}
