<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:35
 */

/**
 * @brief DoAction
 * 后台主控制器，包括基本上的后台操作。
 */
class DoAction extends CommonAction
{
    private $MUser;
    private $admin_data;

    private function init()
    {

    }

    /**
     * @brief 检查是否登录
     * @return bool
     */
    private function check_online()
    {
        $this->admin_data = $this->MUser->check_online();
        return null != $this->admin_data;
    }

    public function __construct()
    {
        /** 基类构造函数 */
        parent::__construct();

        /** 初始化模型 */
        $this->MUser = new UserModel("user");

        /** 若木有登录则跳转到登录界面 */
        if (!$this->check_online()) {
            redirect(U("security/login"));
            die();
        }
    }

    /**
     * 后台首页
     * @return void
     */
    public function index()
    {
        //dump($this->admin_data);
    }
}
