<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-12-11 下午8:35
 */

/**
 * @brief ContestAction
 * 比赛控制器
 */
class ContestAction extends CommonAction
{
    private $contest_model;
    private $contestuser_model;
    private $contestproblem_model;
    private $problem_model;

    public function __construct()
    {
        parent::__construct();

        $this->contestuser_model = new ContestUserModel("contestuser");
        $this->contestproblem_model = new ContestProblemModel("contestproblem");
        $this->contest_model = new ContestModel("contest");
        $this->problem_model = new ProblemModel("problem");
    }

    public function index()
    {
        /** 分页信息 */
        $page_num = $_GET["page"];
        if(null == $page_num || !is_numeric($page_num)) $page_num = 1;
        $per_page = C("CONTEST_NUM_PER_PAGE");
        $tot_num = $this->contest_model->count();
        $pages = (int)((int)$tot_num / (int)$per_page) + (($tot_num % $per_page == 0) ? 0 : 1);
        if($page_num > $pages) $page_num = $pages;
        $_GET["page"] = $page_num;
        $this->web_config["title"] .= " 比赛列表 :: 第 " . $page_num . " 页";
        $this->assign("HC", $this->web_config);

        /** 页码字符串 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("OnlineJudge://Contest@") . "?page=%s";
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

    public function view()
    {
        $contestid = $_GET["id"];

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false == $contest_info || $contestid == 1)
        {
            redirect("OnlineJudge://Problem@");
            die(0);
        }

        /** 时间 */
        $current_time = time();

        /** 时间未到 */
        if($current_time < $contest_info["starttime"])
        {
            $this->_viewcontest_not_started($contest_info);
            die(0);
        }

        /** 比赛中... */

        /** 是否是浏览题目界面 */
        $index = $_GET["problem"];
        $problem_info = array();
        if($index != "" && $index != null)
        {
            $problem_info = $this->contestproblem_model->get_problem_by_index($contestid, $index);
        }
        if($index == "" || $index == null || $problem_info === false)
        {
            $this->_viewcontest($contest_info);
            die(0);
        }
    }

    private function _viewcontest($contest_info)
    {
        $problem_list = $this->contestproblem_model->get_problems_by_page($contest_info["contestid"], 1, 65535, "", "`index` asc");
        $this->web_config["title"] .= " 比赛信息 :: {$contest_info['title']}";
        $this->assign("HC", $this->web_config);
        $this->assign("info", $contest_info);
        $this->assign("prob_list", $problem_list);
        $this->display("viewcontest");
    }

    /**
     * 浏览比赛信息【时间未到】
     * @return void
     */
    private function _viewcontest_not_started($contest_info)
    {
        $this->web_config["title"] .= " 未开始比赛 :: {$contest_info['title']}";
        $this->assign("HC", $this->web_config);
        $this->assign("info", $contest_info);
        $this->display("viewcontest_not_started");
    }
}
