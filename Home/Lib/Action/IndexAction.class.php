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
}
