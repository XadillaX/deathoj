<?php
/**
 * 前台首页
 * User: konakona
 * Date: 11-10-31
 * Time: 下午1:58
 * $Id$
 */

class IndexAction extends CommonAction
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
        $this->web_config["title"] .= "Index";
        $this->assign("HC", $this->web_config);
        
        $this->display();
    }

    /**
     * 跑到某个题目里去
     * $Id$
     * @return void
     */
    public function gotoproblem()
    {
        $contest_problem_model = new ContestProblemModel("contestproblem");
        $info = $contest_problem_model->get_problem_by_index(1, $_POST["problemid"]);

        if(false == $info) redirect(__ROOT__ . "/");
        else redirect(U("Problem/view") . "?id={$_POST['problemid']}");
    }
}
