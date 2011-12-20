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
    private $submit_model;
    private $per_page;                      ///< 每页题数
    private $status_per_page;

    public function __construct()
    {
        parent::__construct();

        $this->contestproblem_model = new ContestProblemModel("contestproblem");
        $this->contest_model = new ContestModel("contest");
        $this->submit_model = new SubmitModel("submit");

        $this->per_page = C("PROBLEM_SET_PER_PAGE");
        $this->status_per_page = C("STATUS_NUM_PER_PAGE");
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
        $page_count = (int)((int)$prob_count / (int)$this->per_page) + (($prob_count % $this->per_page == 0) ? 0 : 1);
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

    /**
     * 代码提交页
     * @return void
     */
    public function submit()
    {
        /** 若未登录 */
        if(null == $this->get_current_user())
        {
            redirect(U("User/login") . "?url=" . urlencode(__SELF__));
            die(0);
        }

        $language_model = new Model("language");
        $lang = $language_model->select();
        $this->assign("lang_list", $lang);

        $this->web_config["title"] .= " 提交代码";
        $this->assign("HC", $this->web_config);
        $this->display();
    }

    /**
     * 代码提交验证页
     * @return void
     */
    public function submitok()
    {
        /** 若未登录 */
        if(null == $this->get_current_user())
        {
            $this->error("您尚未未登录。", true);
            die(0);
        }

        /** 不存在此语言 */
        $language_model = new Model("language");
        if(false == $language_model->where(array("languageid" => $_POST["language"])))
        {
            $this->error("木有此语言。", true);
            die(0);
        }

        /** 不存在此题目 */
        $prob_info = $this->contestproblem_model->get_problem_by_index($this->contestid, $_POST["id"]);
        if(false == $prob_info)
        {
            $this->error("木有此题目。", true);
            die(0);
        }

        /** 该“习题库”不允许此语言 */
        $contest_info = $this->contest_model->get_contest_info($this->contestid);
        $valid_lang = explode("|", $contest_info["language"]);
        if(!in_array($_POST["language"], $valid_lang))
        {
            $this->error("该“习题库”不允许此语言。", true);
            die(0);
        }

        /** 获取下一个submitid */
        $submit_model = new SubmitModel("submit");
        $submitid = $this->contest_model->get_next_submitid($this->contestid);
        if(false === $submitid || null === $submitid)
        {
            $this->error("系统错误。" . $this->contest_model->getLastSql(), true);
            die(0);
        }
        else
        {
            /** 用户信息 */
            $userinfo = $this->get_current_user();

            /** 新增submit */
            $result = $submit_model->new_submit(
                $submitid,
                $this->contestid,
                $_POST["id"],
                $userinfo["userid"],
                $_POST["code"],
                $_POST["language"]
            );
            
            if($result != false)
            {
                /** 对于用户的submit+1 */
                $this->user_model->add_submit($userinfo["userid"]);
                $this->contestproblem_model->add_submit($this->contestid, $_POST["id"]);
                $this->user_model->change_default_language($userinfo["userid"], $_POST["language"]);
                
                /** 对于用户的submitlist增加 */
                $list_str = $this->user_model->get_submit_list($userinfo["userid"]);
                $list = explode("|", $list_str);
                if(!in_array($_POST["id"], $list))
                {
                    $list_str .= ($_POST["id"] . "|");
                    $this->user_model->modify_submit_list($userinfo["userid"], $list_str);
                }

                $this->success("提交成功。", true);
                die(0);
            }
            else
            {
                $this->error("系统错误。" . $submit_model->getLastSql(), true);
                die(0);
            }
        }
    }

    public function status()
    {
        /** 分页信息 */
        $page = $_GET["page"];
        if(!is_numeric($page)) $page = 1;
        $prob_count = $this->submit_model->get_count($this->contestid);
        $page_count = (int)((int)$prob_count / (int)$this->status_per_page) + (($prob_count % $this->status_per_page == 0) ? 0 : 1);
        if($page > $page_count && $page_count != 0) $page = $page_count;

        /** 分页对象 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("Problem/status") . "?page=%s";
        $page_obj->per_page = $this->status_per_page;              ///< 每页数量
        $page_obj->item_count = $prob_count;                ///< 记录数
        $page_obj->cur_page = $page;                        ///< 当前页码
        $page_obj->id = "xpage";
        $page_str = $page_obj->create_links();
        $this->assign("page_str", $page_str);

        $this->web_config["title"] .= "运行状态 :: 第 {$page} 页";
        $this->assign("HC", $this->web_config);

        /** 提交列表 */
        $list = $this->submit_model->get_submit_by_page($this->contestid, $page, $this->status_per_page);
        $this->assign("submit_list", $list);

        $this->display();
    }

    /**
     * 浏览编译错误信息
     * @return void
     */
    public function viewce()
    {
        $contestid = $_GET["contestid"];
        $submitid = $_GET["submitid"];

        if(!is_numeric($contestid)) $contestid = 1;
        if(!is_numeric($submitid)) $submitid = 1;

        $data = $this->submit_model->get_submit_info($contestid, $submitid);

        /** 木有数据 */
        if(false == $data) exit(0);

        /** 木有登录 */
        if($this->user_information == null)
        {
            exit(0);
        }

        /** 用户对不上号 */
        if($this->user_information["userid"] != $data["userid"] && $this->user_information["roleid"] != 3)
        {
            exit(0);
        }

        echo "<pre>" . $data["message"] . "</pre>";
    }

    /**
     * 查看自己的代码
     * @return void
     */
    public function viewcode()
    {
        $contestid = $_GET["contestid"];
        $submitid = $_GET["submitid"];

        if(!is_numeric($contestid)) $contestid = 1;
        if(!is_numeric($submitid)) $submitid = 1;

        $data = $this->submit_model->get_submit_info($contestid, $submitid);

        /** 木有数据 */
        if(false == $data)
        {
            redirect(__ROOT__ . "/");
            die(0);
        }

        /** 木有权限 */
        $data["can_view"] = true;
        if($this->user_information == null || ($this->user_information["roleid"] != 3 && $this->user_information["userid"] != $data["userid"]))
        {
            $data["can_view"] = false;
        }
        else $data["code"] = $this->submit_model->CodeEncode($data["code"]);

        $this->web_config["title"] .= ("查看源代码 #" . $data["submitid"]);
        $this->assign("HC", $this->web_config);
        $this->assign("submit", $data);

        $this->display();
    }

    /**
     * 题目状态统计
     * @return void
     */
    public function statistic()
    {
        $index = $_GET["id"];

        /** 若木有这个题目 */
        $problem = $this->contestproblem_model->get_problem_by_index(1, $index);
        if(false === $problem)
        {
            redirect(__ROOT__ . "/");
        }

        $this->assign("problem_info", $problem);

        /** 获取统计信息 */
        $result_model = new Model("result");
        $analyze = $this->submit_model->get_statistic($index);
        $result_info = $result_model->select();
        $newanalyze = array();
        for($i = 0; $i < count($result_info); $i++) $newanalyze[$result_info[$i]["result"]] = 0;
        for($i = 0; $i < count($analyze); $i++)
        {
            if("TIME_LIMIT_EXCEEDED" != $analyze[$i]["result"])
                $newanalyze[$analyze[$i]["result"]] = $analyze[$i]["count"];
            else $newanalyze[$analyze[$i]["result"]] += $analyze[$i]["count"];
        }

        /** 获取分页信息 */
        $page = $_GET["page"];
        if(!is_numeric($page)) $page = 1;
        $submit_count = $this->submit_model->where(array("contestid" => $this->contestid, "index" => $index, "resultid" => 3))->count();
        $page_count = (int)((int)$submit_count / (int)$this->status_per_page) + (($submit_count % $this->status_per_page == 0) ? 0 : 1);
        if($page > $page_count && $page_count != 0) $page = $page_count;

        /** 分页对象 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("Problem/statistic") . "?page=%s&id=" . $index;
        $page_obj->per_page = $this->status_per_page;              ///< 每页数量
        $page_obj->item_count = $submit_count;                ///< 记录数
        $page_obj->cur_page = $page;                        ///< 当前页码
        $page_obj->id = "xpage";
        $page_str = $page_obj->create_links();
        $this->assign("page_str", $page_str);

        $this->web_config["title"] .= "状态统计 - P{$index} :: 第 {$page} 页";
        $this->assign("HC", $this->web_config);

        /** 提交列表 */
        $submit_list = $this->submit_model->get_submit_by_page($this->contestid, $page, $this->status_per_page, $index, true, "time ASC, memory ASC, length ASC");

        /** RANK */
        for($i = 0; $i < count($submit_list); $i++)
        {
            $submit_list[$i]["rank"] = ($page - 1) * $this->status_per_page + $i + 1;
        }

        $this->assign("analyze", $newanalyze);
        $this->assign("submit_list", $submit_list);

        $this->display();
    }
}
