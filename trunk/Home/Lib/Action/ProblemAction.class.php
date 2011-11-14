<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-14 下午10:09
 */

/**
 * @brief ProblemAction
 * 练习题库控制器
 */
class ProblemAction extends CommonAction
{
    private $contestid = 1;                 ///< 练习题库的contestid
    private $contest_model;
    private $problem_model;
    private $contestproblem_model;
    private $per_page;                      ///< 每页题数

    public function __construct()
    {
        parent::__construct();

        $this->contestproblem_model = new ContestProblemModel("contestproblem");

        $this->per_page = C("PROBLEM_SET_PER_PAGE");
    }

    /**
     * 题目目录页
     * @return void
     */
    public function index()
    {
        /** 分页信息 */
        $page = $_GET["page"];
        if(!is_numeric($page)) $page = 1;
        $prob_count = $this->contestproblem_model->where("contestid = {$this->contestid}")->count();
        $page_count = (int)((int)$prob_count / (int)$this->per_page) + ($prob_count % $this->per_page == 0) ? 0 : 1;
        if($page > $page_count) $page = $page_count;

        /** 分页对象 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("Problem@") . "?page=%s";
        $page_obj->per_page = $this->per_page;              ///< 每页数量
        $page_obj->item_count = $prob_count;                ///< 记录数
        $page_obj->cur_page = $page;                        ///< 当前页码
        $page_str = $page_obj->create_links();
        $this->assign("page_str", $page_str);

        $this->web_config["title"] .= "题库目录 :: 第 {$page} 页";
        $this->assign("HC", $this->web_config);

        /** 题目列表 */
        $list = $this->contestproblem_model->get_problems_by_page($this->contestid, $page, $this->per_page);
        $this->assign("prob_list", $list);

        $this->display();
    }

    /**
     * 题目内页
     * @return void
     */
    public function view()
    {
        /** 获取题目资料 */
        $index = $_GET["id"];
        $info = $this->contestproblem_model->get_problem_by_index($this->contestid, $index);
        $this->assign("info", $info);

        /** 赋值模板变量 */
        $this->web_config["title"] .= "[{$info["index"]}] {$info["title"]}";
        $this->assign("HC", $this->web_config);

        $this->display();
    }
}
