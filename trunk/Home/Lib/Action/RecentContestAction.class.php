<?php
/**
 * 近期比赛
 * User: XadillaX
 * Date: 12-03-27
 * Time: 下午1:58
 * $Id$
 */

class RecentContestAction extends CommonAction
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 首页
     * $Id$
     * @return void
     */
    public function index()
    {
        $json = file_get_contents("http://contests.acmicpc.info/contests.json");
        $arr = json_decode($json, true);
        
        $this->web_config["title"] .= "Recent Contest(s)";
        $this->assign("contests", $arr);
        $this->assign("HC", $this->web_config);
        
        $this->display();
    }
}
