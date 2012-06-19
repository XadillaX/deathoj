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
		$page_obj->id = "xpage";
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
        else
        {
            /** 比赛用户不允许提交 */
            $userinfo = $this->get_current_user();
            if($userinfo["roleid"] == -100)
            {
                $this->error("比赛用户不允许在此提交。", true);
                die(0);
            }
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
            $this->error("系统错误。", true);
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
        $query_condition = array();
        
        /** Query用户名 */
        if(isset($_GET["username"]) && $this->common_str_validate($_GET["username"], 4, 32))
        {
            $query_condition["username"] = $_GET["username"];
        }
        /** Query题号 */
        if(isset($_GET["problemid"]) && $_GET["problemid"] != "" && is_numeric($_GET["problemid"]))
        {
            $query_condition["index"] = $_GET["problemid"];
        }
        /** Query结果 */
        if(isset($_GET["result"]) && $_GET["result"] != "" && is_numeric($_GET["result"]))
        {
            if($_GET["result"] == 7 || $_GET["result"] == 8)
            {
                $query_condition["resultid"] = array("in", "7, 8");
            }
            else $query_condition["resultid"] = $_GET["result"];
        }
        /** Query语言 */
        if(isset($_GET["language"]) && $_GET["language"] != "" && is_numeric($_GET["language"]))
        {
            $query_condition["languageid"] = $_GET["language"];
        }

        /** 分页信息 */
        $page = $_GET["page"];
        if(!is_numeric($page)) $page = 1;
        $prob_count = $this->submit_model->get_count($this->contestid, count($query_condition) == 0 ? null : $query_condition);
        $page_count = (int)((int)$prob_count / (int)$this->status_per_page) + (($prob_count % $this->status_per_page == 0) ? 0 : 1);
        if($page > $page_count && $page_count != 0) $page = $page_count;

        /** 分页对象 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("Problem/status") . "?page=%s";
        if(isset($query_condition["username"])) $page_obj->link_str .= ("&username=" . $query_condition["username"]);
        if(isset($query_condition["index"])) $page_obj->link_str .= ("&problemid=" . $query_condition["index"]);
        if(isset($query_condition["resultid"])) $page_obj->link_str .= ("&result=" . $query_condition["resultid"]);
        if(isset($query_condition["languageid"])) $page_obj->link_str .= ("&language=" . $query_condition["languageid"]);

        $page_obj->per_page = $this->status_per_page;              ///< 每页数量
        $page_obj->item_count = $prob_count;                ///< 记录数
        $page_obj->cur_page = $page;                        ///< 当前页码
        $page_obj->id = "xpage";
        $page_str = $page_obj->create_links();
        $this->assign("page_str", $page_str);

        $this->web_config["title"] .= "运行状态 :: 第 {$page} 页";
        $this->assign("HC", $this->web_config);

        /** 提交列表 */
        $list = $this->submit_model->get_submit_by_page($query_condition, $this->contestid, $page, $this->status_per_page);
        $this->assign("submit_list", $list);

        //dump($this->submit_model->getLastSql());

        $this->display();
    }

    /**
     * 浏览编译错误信息
     * @return void
     */
    public function viewce()
    {
        $contestid = $_GET["cid"];
        $submitid = $_GET["submitid"];

        if(!is_numeric($contestid)) $contestid = 1;
        if(!is_numeric($submitid)) $submitid = 1;

        $data = $this->submit_model->get_submit_info($contestid, $submitid);

        /** 木有数据 */
        $msgs = array();
        if(false == $data)
        {
            $msgs[0] = "没有此数据。";
        }
        else
        /** 木有登录 */
        if($this->user_information == null)
        {
            $msgs[0] = "你没有登录。";
        }
        else
        /** 用户对不上号 */
        if($this->user_information["userid"] != $data["userid"] && $this->user_information["roleid"] != 3)
        {
            $msgs[0] = "你无权查看此CE数据。";
        }
        else
        /** 不是CE */
        if($data["resultid"] != 13 && $data["resultid"] != 11)
        {
            $msgs[0] = "此提交并非CE或者SE。";
        }
        else
        {
            /** 处理数据 */
            if($data["languageid"] == 1 || $data["languageid"] == 2)
            {
                /** C或者C++ */
                $msgs = explode("\n", $data["message"]);
                unset($msgs[count($msgs) - 1]);

                /** 要删除的字 */
                $cmpstr[0] = "compilers/C/";
                $cmpstr[1] = "tmpdir/";
                $cmplen[0] = strlen($cmpstr[0]);
                $cmplen[1] = strlen($cmpstr[1]);
                $msgs_count = count($msgs);

                for($i = 0; $i < $msgs_count; $i++)
                {
                    if(substr($msgs[$i], 0, $cmplen[0]) == $cmpstr[0])
                    {
                        $msgs[$i] = substr($msgs[$i], $cmplen[0]);
                    }
                    else
                    if(substr($msgs[$i], 0, $cmplen[1]) == $cmpstr[1])
                    {
                        $msgs[$i] = substr($msgs[$i], $cmplen[1]);
                    }
                }
            }
        }

        /** 若不是CE */
        $msg = implode("\n", $msgs);
        $this->assign("msg", $msg);

        $this->display();
    }

    /**
     * 查看自己的代码
     * @return void
     */
    public function viewcode()
    {
        $contestid = $_GET["cid"];
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
        $PREFIX = C("DB_PREFIX");
        $page = $_GET["page"];
        if(!is_numeric($page)) $page = 1;
        $submit_count = $this->submit_model->query("SELECT count(*) as tp_count FROM (SELECT userid FROM `{$PREFIX}submit` WHERE ( `contestid` = 1 ) AND ( `index` = '{$index}' ) AND ( `resultid` = 3 ) group by userid) as temp LIMIT 1");//$this->submit_model->where(array("contestid" => $this->contestid, "index" => $index, "resultid" => 3))->count("userid");
        $submit_count = $submit_count[0]["tp_count"];
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
        //$submit_list = $this->submit_model->get_submit_by_page(array(), $this->contestid, $page, $this->status_per_page, $index, true, "time ASC, memory ASC, length ASC");
        $submit_list = $this->submit_model->get_best_solution(1, $page, $this->status_per_page, $index);

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
