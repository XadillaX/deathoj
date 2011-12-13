<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-11-14 上午9:17
 */

/**
 * @brief PracticeAction
 * 练习题库控制器
 */
class PracticeAction extends CommonAction
{
    private $contestid = 1;                    ///< 练习题库的contestid固定为1
    private $language = array("GCC", "G++");  ///< 允许的编译器为C和C++
    private $cpmodel;
    private $contest_model;
    private $problem_model;

    public function __construct()
    {
        parent::__construct();

        $this->problem_model = new ProblemModel("problem");
        $this->cpmodel = new ContestProblemModel("contestproblem");
        $this->contest_model = new ContestModel("contest");
    }

    /**
     * 首页
     * @version $Id$
     * @return void
     */
    public function index()
    {
        /** 获取数据  */
        $info = $this->contest_model->get_contest_info($this->contestid);
        if(false == $info)
        {
            redirect(U("Do/index"));
            die(0);
        }
        $list = $this->cpmodel->get_all_problems($this->contestid);
        $this->assign("prob_list", $list);
        $this->assign("contest_info", $info);

        /** 标题 */
        $this->web_config["title"] .= " {$info['title']} :: 题目管理";

        /** 所属分类 */
        $this->web_config["action_class"] = "contest";
        $this->web_config["sub_action"] = "practice";
        $this->assign("HC", $this->web_config);
        $this->assign("admin_information", $this->admin_information);

        $this->display();
    }

    public function edit_prob()
    {
        $index = $_POST["index"];
        $problemid = $_POST["problemid"];
        $contestproblemid = $_POST["contestproblemid"];

        /** 是否有这个题目 */
        $result = $this->problem_model->get_problem_by_id($problemid);
        if(false == $result)
        {
            $this->error("不存在的题目编号。", true);
            die(0);
        }

        /** 是否有练习题库 */
        $info = $this->contest_model->get_contest_info($this->contestid);
        if(false == $info)
        {
            $this->error("不存在练习题库。", true);
            die(0);
        }
        $prob_info = $this->cpmodel->get_problem_by_id($contestproblemid);
        if(false == $prob_info)
        {
            $this->error("不存在此练习题目信息。", true);
            die(0);
        }
        if($problemid == $prob_info["problemid"] && $index == $prob_info["index"])
        {
            $this->success("木有任何改变，不需要保存。", true);
            die(0);
        }

        $result = $this->cpmodel->edit_problem($this->contestid, $contestproblemid, $index, $problemid);
        if(false == $result)
        {
            $this->error("系统错误，可能是已存在这个索引。" . $this->cpmodel->getError(), true);
            die(0);
        }
        else
        {
            $this->success("编辑成功！", $result);
            die(0);
        }
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

        /** 是否有这个题目 */
        $result = $this->problem_model->get_problem_by_id($problemid);
        if(false == $result)
        {
            $this->error("不存在的题目编号。", true);
            die(0);
        }

        /** 是否有练习题库 */
        $info = $this->contest_model->get_contest_info($this->contestid);
        if(false == $info)
        {
            $this->error("不存在练习题库。", true);
            die(0);
        }

        $result = $this->cpmodel->add_problem($this->contestid, $index, $problemid);
        if(false == $result)
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
}
