<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:37
 */

/**
 * @brief IndexAction
 * 后台导向控制器，导向登录页面或者后台主控制器。
 */
class IndexAction extends Action
{
    /**
     * @brief 跳转到登陆页面或者操作页面
     * @return void
     */
    public function index()
    {
        $MUser = new UserModel("user");

        /** 检查是否登录 */
        if (null == $MUser->check_online()) redirect(U("security/login"));
        else redirect(U("Do/index"));

        die(0);
    }
}