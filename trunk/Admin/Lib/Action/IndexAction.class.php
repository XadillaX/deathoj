<?php
/**
 * NBUT Online Judge System
 *
 * 后台导向控制器（导向Do/index）
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:35
 * @package Index
 */
class IndexAction extends CommonAction
{

    /**
     * @brief 跳转到登陆页面或者操作页面
     * @return void
     */
    public function index()
    {
        redirect(U("Do/index"));
    }
}
