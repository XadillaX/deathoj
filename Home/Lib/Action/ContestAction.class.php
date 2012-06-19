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
    private $submit_model;
    private $status_per_page;

    private function get_fine_str($time)
    {
        $hour = (int)((int)$time / (int)3600);
        $time = $time % 3600;
        $minute = (int)((int)$time / (int)60);
        $second = $time % 60;

        if(strlen($hour) < 2) $hour = "0" . $hour;
        if(strlen($minute) < 2) $minute = "0" . $minute;
        if(strlen($second) < 2) $second = "0" . $second;

        return $hour . ":" . $minute . ":" . $second;
    }

    function get_percent($ac, $sub)
    {
        if($ac == 0) return "0.00%";
        else
        {
            $result = sprintf("%.2f%%", ($ac / $sub) * 100);
            return $result;
        }
    }

    /**
     * @param $starttime 比赛开始时间
     * @param $endtime 比赛结束时间
     * @param $now 当前时间
     * @return int 若为-1则未开始，若为0则进行中，若为1则结束
     */
    private function get_contest_state($starttime, $endtime, $now)
    {
        if($now < $starttime) return -1;
        if($now > $endtime) return 1;
        return 0;
    }

    /**
     * 底部bar
     * @param $contestid
     * @return void
     */
    private function make_bar($contestid, $is_user = true, $showprob = true)
    {
        $bar = array();
        $bar["problem"] = U("Contest/view?id=" . $contestid);
        $bar["statistics"] = U("Contest/statistics?id=" . $contestid);
        $bar["rank"] = U("Contest/rank?id=" . $contestid);
        $bar["status"] = U("Contest/status?id=" . $contestid);

        if($showprob)
        {
            $bar["problems"] = array();
            $temp = $this->contestproblem_model->get_all_problems_with_info($contestid);
            $temp_count = count($temp);
            for($i = 0; $i < $temp_count; $i++)
            {
                $bar["problems"][$i]["index"] = $temp[$i]["index"];
                $bar["problems"][$i]["url"] = U("Contest/view?id={$contestid}&problem={$temp[$i]['index']}");
                $bar["problems"][$i]["title"] = $is_user ? $temp[$i]["title"] : "您非本比赛用户";

                $bar["problems"][$i]["ac"] = $temp[$i]["solved"];
                $bar["problems"][$i]["sub"] = $temp[$i]["submit"];
                $bar["problems"][$i]["percent"] = $this->get_percent($temp[$i]["solved"], $temp[$i]["submit"]);
            }
        }

        /** 是否要报名 */
        $bar["needsignup"] = false;
        $login_user = $this->get_current_user();
        if($login_user == null || ($login_user != null && !$this->contestuser_model->is_user_joined($contestid, $login_user["userid"])))
        {
            $bar["needsignup"] = true;
            $bar["signup"] = U("Contest/signup?id=" . $contestid);
        }
        else
        {
            $bar["teamname"] = $this->contestuser_model->get_teamname($contestid, $login_user["userid"]);
            $bar["userid"] = $login_user["userid"];

            /** 已做的题目和已AC的题目 */
            $this->submit_model = new SubmitModel("submit");
            $condition = array("userid" => $login_user["userid"], "contestid" => $contestid);
            $submited = $this->submit_model->field("`index`")->where($condition)->group("`index`")->order("`index` asc")->select();

            $condition["resultid"] = 3;
            $aced = $this->submit_model->field("`index`")->where($condition)->group("`index`")->order("`index` asc")->select();

            for($i = 0; $i < count($submited); $i++) $submited[$i] = $submited[$i]["index"];
            for($i = 0; $i < count($aced); $i++) $aced[$i] = $aced[$i]["index"];

            $bar["submited"] = $submited;
            $bar["aced"] = $aced;
        }

        //dump($this->contestuser_model->is_user_joined($contestid, $login_user["userid"]));

        $this->assign("bar", $bar);
    }

    /**
     * 确认报名参赛
     * @return void
     */
    public function validate_signup()
    {
        $contestid = $_POST["cid"];

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false == $contest_info || $contestid == 1)
        {
            redirect("OnlineJudge://Problem@");
            die(0);
        }

        /** 比赛已结束 */
        if($this->get_contest_state($contest_info["starttime"], $contest_info["endtime"], time()) > 0)
        {
            $this->alert_redirect("比赛已结束。", -1);
            die(0);
        }
       

        /** 是否已登录 */
        $login_user = $this->get_current_user();
        if($login_user == null)
        {
            $this->alert_redirect("您还未登录或者登录过期。", U("User/login") . "?url=" . urlencode(U("Contest/signup?id={$contestid}")), false);
            die(0);
        }
        
        /** 私有比赛 */
        if($contest_info["private"] && $login_user["roleid"] != 3)
        {
            $this->alert_redirect("私有比赛不允许报名。", -1);
            die(0);
        }

        /** 是否规定比赛用户 */
        if($login_user["roleid"] == -100)
        {
            $this->alert_redirect("本用户为别的比赛的保留账户，不能参与别的比赛。", U("Contest/view?id={$contestid}"));
            die(0);
        }

        /** 是否已报名 */
        if($this->contestuser_model->is_user_joined($contestid, $login_user["userid"]))
        {
            $this->alert_redirect("您已经报名了本比赛。", U("Contest/view?id={$contestid}"));
            die(0);
        }

        /** 昵称正确性 */
        if(!$this->common_str_validate($_POST["teamname"], 4, 32, false))
        {
            $this->alert_redirect("队名（昵称）昵称长度必须介于4~32之间。", -1);
            die(0);
        }
        if(!$this->contestuser_model->validate_unique($contestid, $_POST["teamname"]))
        {
            $this->alert_redirect("已存在的队名（昵称），请重填。", -1);
            die(0);
        }

        /** 报名 */
        $res = $this->contestuser_model->signup($contestid, $login_user["userid"], $_POST["teamname"]);
        if($res)
        {
            $this->alert_redirect("报名成功！", U("Contest/view?id={$contestid}"));
            die(0);
        }
        else
        {
            $this->alert_redirect("系统错误，请联系管理员或者稍后再试。", U("Contest/view?id={$contestid}"));
            die(0);
        }
    }

    /**
     * 报名参赛
     * @return void
     */
    public function signup()
    {
        $contestid = $_GET["id"];

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false == $contest_info || $contestid == 1)
        {
            redirect("OnlineJudge://Problem@");
            die(0);
        }

        /** 比赛已结束 */
        if($this->get_contest_state($contest_info["starttime"], $contest_info["endtime"], time()) > 0)
        {
            $this->alert_redirect("比赛已结束。", -1);
            die(0);
        }

        /** 是否已登录 */
        $login_user = $this->get_current_user();
        if($login_user == null)
        {
            $this->alert_redirect("", U("User/login") . "?url=" . urlencode(__SELF__), false);
            die(0);
        }

        /** 是否规定比赛用户 */
        if($login_user["roleid"] == -100)
        {
            $this->alert_redirect("本用户为别的比赛的保留账户，不能参与别的比赛。", U("Contest/view?id={$contestid}"));
            die(0);
        }

        /** 是否已报名 */
        if($this->contestuser_model->is_user_joined($contestid, $login_user["userid"]))
        {
            $this->alert_redirect("您已经报名了本比赛。", U("Contest/view?id={$contestid}"));
            die(0);
        }

        /** 开始报名 */
        $this->make_bar($contestid, false, false);
        $this->web_config["title"] .= " 报名比赛 :: {$contest_info['title']}";
        $this->assign("HC", $this->web_config);
        $this->assign("contest_info", $contest_info);

        $this->display();
    }

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
    
    /** 比赛排名 */
    public function rank()
    {
        $contestid = $_GET["id"];
        
        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false == $contest_info || $contestid == 1)
        {
            redirect("OnlineJudge://Problem@");
            die(0);
        }

        /** 比赛是否开始 */
        if($this->get_contest_state($contest_info["starttime"], $contest_info["endtime"], time()) < 0)
        {
            $this->_viewcontest_not_started($contest_info);
            die(0);
        }
        
        $filename = C("RANK_PATH") . "\\{$contestid}-{$contest_info['resultversion']}.php";
        $this->make_bar($contestid);
        
        if(!file_exists($filename))
        {
            /** 排名缓存文件不存在 */
            $this->web_config["title"] .= " {$contest_info['title']} :: Rank";
            $this->assign("HC", $this->web_config);
            $this->assign("contest_info", $contest_info);

            $this->display("rank_no_cache");
        }
        else
        {
            require_once $filename;
            $rank_info;

            /** 取出比赛用户信息 */
            $contest_users = $this->contestuser_model->get_user_list($contestid);
            $user_count = count($contest_users);
            $rank_count = count($rank_info);
            for($i = 0; $i < $user_count; $i++)
            {
                $exist = false;
                for($j = 0; $j < $rank_count; $j++)
                {
                    if($rank_info[$j]["userid"] == $contest_users[$i]["userid"])
                    {
                        $rank_info[$j]["teamname"] = $contest_users[$i]["teamname"];
                        $rank_info[$j]["username"] = $contest_users[$i]["username"];
                        $rank_info[$j]["nickname"] = $contest_users[$i]["nickname"];
                        $rank_info[$j]["rank"] = $j + 1;
                        $rank_info[$j]["finestr"] = $this->get_fine_str($rank_info[$j]["time"]);
                        $exist = true;

                        break;
                    }
                }

                /** 此用户还没rank */
                if(false == $exist)
                {
                    $tmp_count = count($rank_info);
                    $rank_info[$tmp_count]["teamname"] = $contest_users[$i]["teamname"];
                    $rank_info[$tmp_count]["username"] = $contest_users[$i]["username"];
                    $rank_info[$tmp_count]["nickname"] = $contest_users[$i]["nickname"];
                    $rank_info[$tmp_count]["userid"] = $contest_users[$i]["userid"];
                    $rank_info[$tmp_count]["acnum"] = $rank_info[$tmp_count]["time"] = 0;
                    $rank_info[$tmp_count]["rank"] = $tmp_count + 1;
                    $rank_info[$tmp_count]["finestr"] = "00:00:00";
                }
            }

            /** 题目列表 */
            $problem_list = $this->contestproblem_model->get_all_problems($contestid);

            $this->web_config["title"] .= " {$contest_info['title']} :: Rank";
            $this->assign("HC", $this->web_config);
            $this->assign("contest_info", $contest_info);
            $this->assign("rank_info", $rank_info);
            $this->assign("problem_list", $problem_list);
            $this->display("rank");
        }
    }

    /**
     * 统计
     * @return void
     */
    public function statistics()
    {
        $login_user_info = $this->get_current_user();
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
        $cstate = $this->get_contest_state($contest_info["starttime"], $contest_info["endtime"], $current_time);

        /** 时间未到 */
        if($cstate == -1)
        {
            $this->_viewcontest_not_started($contest_info);
            die(0);
        }
        else
        /** 比赛中... */
        if($cstate == 0)
        {
            /** 若是私有比赛，且用户不在比赛列表 */
            if($contest_info["private"] == 1)
            {
                if($login_user_info == null ||
                   ($login_user_info["roleid"] < 2 && $this->contestuser_model->is_user_joined($contestid, $login_user_info["userid"]) == NULL))
                {
                    $this->make_bar($contestid, false, true);
                }
                else $this->make_bar($contestid);
            }
            else $this->make_bar($contestid);
        }
        else $this->make_bar($contestid);

        /** 所有还是各题 */
        $index = $_GET["problem"];
        $problem_info = array();
        if($index != "" && $index != null)
        {
            $problem_info = $this->contestproblem_model->get_problem_by_index($contestid, $index);
        }

        if($index == "" || $index == null || $problem_info === false)
        {
            $this->_statisticsindex($contest_info);
            die(0);
        }
        else
        if($problem_info != false)
        {
            $this->_statisticsproblem($contest_info, $problem_info);
            die(0);
        }
    }

    private function _statisticsproblem($contest_info, $problem_info)
    {
        $this->assign("problem_info", $problem_info);
        
        /** 获取统计信息 */
        $this->submit_model = new SubmitModel("submit");
        $result_model = new Model("result");
        $analyze = $this->submit_model->get_statistic($problem_info["index"], $contest_info["contestid"]);
        $result_info = $result_model->select();
        $newanalyze = array();
		$index = $problem_info["index"];
        for($i = 0; $i < count($result_info); $i++) $newanalyze[$result_info[$i]["result"]] = 0;
        for($i = 0; $i < count($analyze); $i++)
        {
            if("TIME_LIMIT_EXCEEDED" != $analyze[$i]["result"])
                $newanalyze[$analyze[$i]["result"]] = $analyze[$i]["count"];
            else $newanalyze[$analyze[$i]["result"]] += $analyze[$i]["count"];
        }

        /** 获取分页信息 */
        $this->status_per_page = C("STATUS_NUM_PER_PAGE");
        $PREFIX = C("DB_PREFIX");
        $page = $_GET["page"];
        if(!is_numeric($page)) $page = 1;
        $submit_count = $this->submit_model->query("SELECT count(*) as tp_count FROM (SELECT userid FROM `{$PREFIX}submit` WHERE ( `contestid` = {$contest_info['contestid']} ) AND ( `index` = '{$index}' ) AND ( `resultid` = 3 ) group by userid) as temp LIMIT 1");//$this->submit_model->where(array("contestid" => $this->contestid, "index" => $index, "resultid" => 3))->count("userid");
        $submit_count = $submit_count[0]["tp_count"];
        $page_count = (int)((int)$submit_count / (int)$this->status_per_page) + (($submit_count % $this->status_per_page == 0) ? 0 : 1);
        if($page > $page_count && $page_count != 0) $page = $page_count;

        /** 分页对象 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("Contest/statistics?id={$contest_info['contestid']}&problem={$problem_info['index']}") . "?page=%s";
        $page_obj->per_page = $this->status_per_page;              ///< 每页数量
        $page_obj->item_count = $submit_count;                ///< 记录数
        $page_obj->cur_page = $page;                        ///< 当前页码
        $page_obj->id = "xpage";
        $page_str = $page_obj->create_links();
        $this->assign("page_str", $page_str);

        $this->web_config["title"] .= "{$contest_info['title']} 状态统计 - {$problem_info['index']} :: 第 {$page} 页";
        $this->assign("HC", $this->web_config);

        /** 提交列表 */
        //$submit_list = $this->submit_model->get_submit_by_page(array(), $this->contestid, $page, $this->status_per_page, $index, true, "time ASC, memory ASC, length ASC");
        $submit_list = $this->submit_model->get_best_solution($contest_info["contestid"], $page, $this->status_per_page, $problem_info["index"]);

        /** RANK */
        for($i = 0; $i < count($submit_list); $i++)
        {
            $submit_list[$i]["rank"] = ($page - 1) * $this->status_per_page + $i + 1;
        }

        $this->assign("analyze", $newanalyze);
        $this->assign("submit_list", $submit_list);

        $this->display("_statisticsproblem");
    }

    private function _statisticsindex($contest_info)
    {
        /** 获取题目 */
        $problems = $this->contestproblem_model->get_all_problems($contest_info["contestid"]);
        $problems_count = count($problems);

        /** 获取各题Statistics */
        $this->submit_model = new SubmitModel("submit");
        $statistic = array();
        $result_model = new Model("result");
        $result_info = $result_model->select();
        for($i = 0; $i < $problems_count; $i++)
        {
            $analyze = $this->submit_model->get_statistic($problems[$i]["index"], $contest_info["contestid"]);

            $newanalyze = array();
            for($j = 0; $j < count($result_info); $j++) $newanalyze[$result_info[$j]["result"]] = 0;
            for($j = 0; $j < count($analyze); $j++)
            {
                if("TIME_LIMIT_EXCEEDED" != $analyze[$j]["result"])
                    $newanalyze[$analyze[$j]["result"]] = $analyze[$j]["count"];
                else $newanalyze[$analyze[$j]["result"]] += $analyze[$j]["count"];
            }

            $statistic[$problems[$i]["index"]] = $newanalyze;
            $statistic[$problems[$i]["index"]]["index"] = $problems[$i]["index"];
        }
        
        $this->assign("analyze", $statistic);
        $this->web_config["title"] .= "{$contest_info['title']} 状态统计";
        $this->assign("HC", $this->web_config);

        $this->display("_statisticsindex");
    }

    public function view()
    {
        $login_user_info = $this->get_current_user();

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
        $cstate = $this->get_contest_state($contest_info["starttime"], $contest_info["endtime"], $current_time);

        /** 时间未到 */
        if($cstate == -1)
        {
            $this->_viewcontest_not_started($contest_info);
            die(0);
        }
        else
        /** 比赛中... */
        if($cstate == 0)
        {
            /** 若是私有比赛，且用户不在比赛列表 */
            if($contest_info["private"] == 1)
            {
                if($login_user_info == null ||
                   ($login_user_info["roleid"] < 2 && $this->contestuser_model->is_user_joined($contestid, $login_user_info["userid"]) == NULL))
                {
                    $this->web_config["title"] .= " 无权查看 :: {$contest_info['title']}";
                    $this->assign("HC", $this->web_config);
                    $this->assign("info", $contest_info);
                    $this->make_bar($contestid, false, true);

                    $this->display("contest_no_permission");
                    exit(0);
                }
                else $this->make_bar($contestid);
            }
            else $this->make_bar($contestid);
        }
        else $this->make_bar($contestid);
        
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
        else
        if($problem_info != false)
        {
            $this->_viewproblem($contest_info, $problem_info);
            die(0);
        }
    }

    private function _viewproblem($contest_info, $problem_info)
    {
        //$problem_list = $this->contestproblem_model->get_problems_by_page($contest_info["contestid"], 1, 65535, "", "`index` asc");
        $this->web_config["title"] .= " {$contest_info['title']} :: {$problem_info['title']}";
        $this->assign("HC", $this->web_config);
        $this->assign("contest_info", $contest_info);
        $this->assign("info", $problem_info);
        $this->display("viewproblem");
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
        //unset($bar["problems"]);
        $this->make_bar($contest_info["contestid"], false, false);

        $this->web_config["title"] .= " 未开始比赛 :: {$contest_info['title']}";
        $this->assign("HC", $this->web_config);
        $this->assign("info", $contest_info);
        $this->display("viewcontest_not_started");
    }

    /**
     * 验证提交题目
     * @return void
     */
    public function submitok()
    {
        $contestid = $_POST["contest"];
        $problemid = $_POST["id"];
        if($contestid == "" || $problemid == "")
        {
            $this->error("非法提交。", true);
            die(0);
        }

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false == $contest_info || $contestid == 1)
        {
            $this->error("不存在的比赛。", true);
            die(0);
        }

        /** 是否有比赛权限 */
        $login_user_info = $this->get_current_user();
        if($login_user_info == null ||
           ($login_user_info["roleid"] < 2 && $this->contestuser_model->is_user_joined($contestid, $login_user_info["userid"]) == NULL))
        {
            $this->error("您不是本比赛参赛者，不能提交。", true);
            die(0);
        }

        /** 是否有本题 */
        $problem_info = $this->contestproblem_model->get_problem_by_index($contestid, $problemid);
        if(false == $problem_info)
        {
            $this->error("对不起，无此题目。", true);
            die(0);
        }

        /** 语言问题 */
        $valid_lang = explode("|", $contest_info["language"]);
        if(!in_array($_POST["language"], $valid_lang))
        {
            $this->error("该比赛不允许此语言。", true);
            die(0);
        }

        /** 比赛时间 */
        if($this->get_contest_state($contest_info["starttime"], $contest_info["endtime"], time()) != 0)
        {
            $this->error("当前非比赛时间。", true);
            die(0);
        }

        /** 获取SubmitID */
        $submit_model = new SubmitModel("submit");
        $submitid = $this->contest_model->get_next_submitid($contestid);
        if(false === $submitid || null === $submitid)
        {
            $this->error("系统错误。", true);
            die(0);
        }
        else
        {
            /** 新增submit */
            $result = $submit_model->new_submit(
                $submitid,
                $contestid,
                $problemid,
                $login_user_info["userid"],
                $_POST["code"],
                $_POST["language"]
            );

            if(false != $result)
            {
                $this->contestproblem_model->add_submit($contestid, $problemid);
                $this->user_model->change_default_language($login_user_info["userid"], $_POST["language"]);
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

    /** 提交题目 */
    public function submit()
    {
        $login_user_info = $this->get_current_user();
        if($login_user_info == null)
        {
            $this->alert_redirect("", U('User/login') . "?url=" . urlencode(__SELF__), false);
        }

        $contestid = $_GET["contest"];
        $problemid = $_GET["prob"];
        if($problemid == "")
        {
            $this->alert_redirect("未指明题目编号。", "-1");
            die(0);
        }

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false == $contest_info || $contestid == 1)
        {
            $this->alert_redirect("不存在的比赛。", "-1");
            die(0);
        }

        if($this->get_contest_state($contest_info["starttime"], $contest_info["endtime"], time()) < 0)
        {
            $this->make_bar($contestid, false, false);
        }
        else
        /** 若是私有比赛，且用户不在比赛列表 */
        if($contest_info["private"] == 1)
        {
            if($login_user_info == null ||
               ($login_user_info["roleid"] < 2 && $this->contestuser_model->is_user_joined($contestid, $login_user_info["userid"]) == NULL))
            {
                $this->make_bar($contestid, false, true);
                $this->web_config["title"] .= " 无权查看 :: {$contest_info['title']}";
                $this->assign("HC", $this->web_config);
                $this->assign("info", $contest_info);

                $this->display("contest_no_permission");
                exit(0);
            }
            else $this->make_bar($contestid);
        }
        else $this->make_bar($contestid);

        $this->make_bar($contestid);

        $language_model = new Model("language");
        $lang = $language_model->select();
        $this->assign("lang_list", $lang);
        $this->web_config["title"] .= " 提交代码 :: {$contest_info['title']}";
        $this->assign("current_user", $login_user_info);
        $this->assign("HC", $this->web_config);
        $this->assign("contestid", $contestid);
        $this->assign("problemid", $problemid);

        $this->display("submit");
    }

    /**
     * 状态页面
     * @return void
     */
    public function status()
    {
        $login_user_info = $this->get_current_user();

        $contestid = $_GET["id"];

        /** 是否有比赛 */
        $contest_info = $this->contest_model->get_contest_info($contestid);
        if(false == $contest_info || $contestid == 1)
        {
            redirect("OnlineJudge://Problem@");
            die(0);
        }

        /**
         * 比赛未开始
         */
        if($this->get_contest_state($contest_info["starttime"], $contest_info["endtime"], time()) < 0)
        {
            $this->_viewcontest_not_started($contest_info);
            die(0);
        }
        /** 若是私有比赛，且用户不在比赛列表 */
        if($contest_info["private"] == 1)
        {
            if($login_user_info == null ||
               ($login_user_info["roleid"] < 2 && $this->contestuser_model->is_user_joined($contestid, $login_user_info["userid"]) == NULL))
            {
                $this->make_bar($contestid, false, true);
            }
            else $this->make_bar($contestid);
        }
        else $this->make_bar($contestid);

        /** ================ 运行状态 =============== */
        $this->status_per_page = C("STATUS_NUM_PER_PAGE");
        $this->submit_model = new SubmitModel("submit");

        $query_condition = array();

        /** Query用户名 */
        if(isset($_GET["username"]) && $this->common_str_validate($_GET["username"], 4, 32))
        {
            $query_condition["username"] = $_GET["username"];
        }
        /** Query题号 */
        if(isset($_GET["problemid"]) && $_GET["problemid"] != "")
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
        $prob_count = $this->submit_model->get_count($contestid, count($query_condition) == 0 ? null : $query_condition);
        $page_count = (int)((int)$prob_count / (int)$this->status_per_page) + (($prob_count % $this->status_per_page == 0) ? 0 : 1);
        if($page > $page_count && $page_count != 0) $page = $page_count;

        /** 分页对象 */
        import("@.Plugin.XPage");
        $page_obj = new XPage();
        $page_obj->link_str = U("Contest/status?id={$contestid}") . "?page=%s";
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
        $list = $this->submit_model->get_submit_by_page($query_condition, $contestid, $page, $this->status_per_page);
        $this->assign("submit_list", $list);
        $this->display();
    }
}
