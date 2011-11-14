<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-10 下午5:05
 */

/**
 * @brief ProblemAction
 * 题库相关
 */
class ProblemAction extends CommonAction
{
    private $problem_model;

    public function __construct()
    {
        parent::__construct();

        $this->problem_model = new ProblemModel("problem");
    }

    /**
     * 题目目录控制器
     * @version $Id$
     * @return void
     */
    public function catalog()
    {
        /** 页码 */
        $page_num = $_GET["page"];
        if(null == $page_num || !is_numeric($page_num)) $page_num = 1;
        $per_page = C("PROBLEM_NUM_PER_PAGE");
        $tot_num = $this->problem_model->count();
        $pages = (int)((int)$tot_num / (int)$per_page) + ($tot_num % $per_page == 0) ? 0 : 1;
        if($page_num > $pages) $page_num = $pages;
        $_GET["page"] = $page_num;
        $this->web_config["title"] .= " 题库列表 :: 第 " . $page_num . " 页";
        Session::set("prob_page_when_back", $page_num);

        /** 所属分类 */
        $this->web_config["action_class"] = "problem";
        $this->web_config["sub_action"] = "list";
        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        /** 页码字符串 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("Problem/catalog") . "?page=%s";
        $page_obj->per_page = $per_page;                ///< 每页数量
        $page_obj->item_count = $tot_num;               ///< 记录数
        $page_obj->cur_page = $page_num;                ///< 当前页码
        $page_str = $page_obj->create_links();
        $this->assign("page_str", $page_str);

        /** 题目们 */
        $data = $this->problem_model->get_catalog($page_num, $per_page);
        $this->assign("cat_data", $data);

        /** 哥要显示啦~ */
        $this->display();
    }
    
    /**
     * 新增题目
     * @version $Id$
     * @return void
     */
    public function add()
    {
        $this->web_config["title"] .= " 添加题目";

        /** 所属分类 */
        $this->web_config["action_class"] = "problem";
        $this->web_config["sub_action"] = "new";

        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        $this->display();
    }

    /**
     * 编辑题目控制器
     * @return void
     */
    public function edit()
    {
        $this->web_config["title"] .= " 编辑题目";
        
        /** 所属分类 */
        $this->web_config["action_class"] = "problem";
        $this->web_config["sub_action"] = "list";
        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        /** 获取题目信息 */
        $problemid = $_GET["problemid"];
        if($problemid == "" || $problemid == null)
        {
            redirect(U("Problem/catalog") . "?page=" . Session::get("prob_page_when_back"));
            die(0);
        }
        $data = $this->problem_model->get_problem_by_id($problemid);
        if(false == $data)
        {
            redirect(U("Problem/catalog") . "?page=" . Session::get("prob_page_when_back"));
            die(0);
        }
        $this->assign("data", $data);

        $this->display();
    }

    /**
     * 检验编辑题目
     * @return void
     */
    public function chkedit()
    {
        $problemid = $_GET["problemid"];

        $data = (array)json_decode($_POST["data"]);

        /** 检验 */
        if(!$this->problem_model->autoCheckToken($data) || !is_numeric($problemid))
        {
            $this->error("非法提交！", true);
            die(0);
        }
        if(null == $data["title"] || "" == trim($data["title"]))
        {
            $this->error("标题不能留空。", true);
            die(0);
        }
        if(!is_numeric($data["timelimit"]) || !is_numeric($data["memorylimit"]))
        {
            $this->error("时间限制和内存限制必须为数字。", true);
            die(0);
        }

        /** 更新数据库 */
        $result = $this->problem_model->edit_problem($problemid, $data);
        if(false == $result)
        {
            $this->error("系统错误，请联系开发人员或者稍后再试。", true);
            die(0);
        }
        else
        {
            $this->success("修改成功", true);
            die(0);
        }
    }

    /**
     * 检验新增题目
     * @version $Id$
     * @return void
     */
    public function chkadd()
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
        if(!is_numeric($data["timelimit"]) || !is_numeric($data["memorylimit"]))
        {
            $this->error("时间限制和内存限制必须为数字。", true);
            die(0);
        }

        /** 写入数据库 */
        $result = $this->problem_model->add_problem($data);
        if(false == $result)
        {
            $this->error("系统错误，请联系开发人员或者稍后再试。", true);
            die(0);
        }
        else
        {
            $this->success("添加成功，题目ID: " . $result, true);
            die(0);
        }
    }

    /**
     * 获取后缀
     * @param $filename
     * @return string
     */
    private function get_extension($filename)
    {
	    $x = explode('.', $filename);
	    return strtolower('.' . end($x));
	}

    /**
     * 获取测试数据内容
     * @return void
     */
    public function get_data()
    {
        $problemid = $_GET['problemid'];
        
        /** 检验 */
        if(!is_numeric($problemid))
        {
            redirect(U("Problem/std_data") . "?problemid=" . $problemid);
            die(0);
        }
        $prob_data = $this->problem_model->get_problem_by_id($problemid);
        if(false == $prob_data)
        {
            redirect(U("Problem/std_data") . "?problemid=" . $problemid);
            die(0);
        }
        if(strtolower($_GET["type"]) != "in" && strtolower($_GET["type"]) != "out")
        {
            redirect(U("Problem/std_data") . "?problemid=" . $problemid);
            die(0);
        }

        $prob_data["io_path"] = C("IO_DATA_PATH") . "\\" . $problemid;
        $filename = $prob_data["io_path"] . "\\data." . $_GET["type"];

        if(!file_exists($filename))
        {
            redirect(U("Problem/std_data") . "?problemid=" . $problemid);
            die(0);
        }
        else
        {
            ob_clean();
            ob_start();
            $mime = 'application/force-download';
            header('Pragma: public'); // required
            header('Expires: 0'); // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: ' . $mime);
            header('Content-Disposition: attachment; filename="data.' . $_GET["type"] . '"');
            header('Content-Transfer-Encoding: binary');
            header('Connection: close');
            ob_end_flush();
            readfile($filename); // push it out
            //dump(headers_list());
            exit();
        }
    }

    /**
     * 标准数据上传控制器
     * @return void
     */
    public function upload_std_data()
    {
        $problemid = (int)$_GET["problemid"];

        /** 检验 */
        if(!is_numeric($problemid))
        {
            $this->error("木有本题目。", true);
            die(0);
        }
        $prob_data = $this->problem_model->get_problem_by_id($problemid);
        if(false == $prob_data)
        {
            $this->error("木有本题目。", true);
            die(0);
        }

        /** 上传 */
        if (!empty($_FILES))
        {
            /** 文件大小 */
            $file_size = round($_FILES['Filedata']['size'] / 1024, 2);
            if($file_size > C("MAX_DATA_SIZE"))
            {
                $this->error("大小超过了限制。");
                die(0);
            }

            /** 路径  */
            $path = C("IO_DATA_PATH") . "\\" . $problemid;
            if(!file_exists($path))
            {
                mkdir($path);
            }

            $file_temp = $_FILES['Filedata']['tmp_name'];
            $file_ext = $this->get_extension($_FILES['Filedata']['name']);
            if($file_ext != ".in" && $file_ext != ".out")
            {
                $this->error("类型只能是*.in或者*.out。", true);
                die(0);
            }

            $targetFile = str_replace('//', '/', $path) . "\\data" . $file_ext;
            move_uploaded_file($file_temp, $targetFile);

            $this->success("上传成功！", true);
        }
        else
        {
            $this->error("EMPTY FILE.", true);
        }
    }

    /**
     * 标准输入输出数据操作控制器
     * @return void
     */
    public function std_data()
    {
        $problemid = $_GET["problemid"];

        /** 检验 */
        if(!is_numeric($problemid))
        {
            redirect(U("Problem/catalog") . "?page=" . Session::get("prob_page_when_back"));
            die(0);
        }
        $prob_data = $this->problem_model->get_problem_by_id($problemid);
        if(false == $prob_data)
        {
            redirect(U("Problem/catalog") . "?page=" . Session::get("prob_page_when_back"));
            die(0);
        }

        /** 查看数据 */
        $prob_data["io_path"] = C("IO_DATA_PATH") . "\\" . $problemid;
        $prob_data["has_input"] = file_exists($prob_data["io_path"] . "\\data.in");
        $prob_data["has_output"] = file_exists($prob_data["io_path"] . "\\data.out");

        $this->web_config["title"] .= " 题目标准数据";

        /** 所属分类 */
        $this->web_config["action_class"] = "problem";
        $this->web_config["sub_action"] = "list";
        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);
        $this->assign("prob_data", $prob_data);

        $this->display();
    }
}
