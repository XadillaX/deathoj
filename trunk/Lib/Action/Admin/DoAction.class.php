<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 死月
 * Date: 11-10-26
 * Time: 下午5:02
 * Do Action of Administrator
 */

class DoAction extends Action
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
        if(!$this->check_online())
        {
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
