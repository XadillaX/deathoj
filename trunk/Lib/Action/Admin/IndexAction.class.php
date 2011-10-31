<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 死月
 * Date: 11-10-26
 * Time: 下午6:07
 * Index Action of Administrator
 * 后台管理导向控制器：登录界面或者后台首页界面。
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
