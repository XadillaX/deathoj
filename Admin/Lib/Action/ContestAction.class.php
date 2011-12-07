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

    public function __construct()
    {
        parent::__construct();

        $this->problem_model = new ProblemModel();
        $this->contest_model = new ContestModel();
        $this->contestproblem_model = new ContestProblemModel();
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
        $result = $this->contest_model->edit_contest($contestid, $data);
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
        $pages = (int)((int)$tot_num / (int)$per_page) + ($tot_num % $per_page == 0) ? 0 : 1;
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
}
